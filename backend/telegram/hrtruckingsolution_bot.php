<?php
error_reporting(0);
//ini_set('display_errors', 'on');
use Telegram\Bot\Api;
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/db.php';
function pre($v)
{
    $v = print_r($v, true);
    if(PHP_SAPI == 'cli'){
        print "$v\r\n";
    }else{
        print "<pre>$v</pre>";
    }
}
class Bot
{
    private $tg, $cfg, $db;
    private $apiUrl = 'https://api.telegram.org/bot';
    private $update, $message, $messageId, $chatId, $user;
    public function __construct($cfg)
    {
        $this->cfg = is_array($cfg) ? $cfg : [];
        $this->tg = new Api($this->cfg('token'));
        $this->db = new Db(new mysqli($this->cfg('mysql')['host'], $this->cfg('mysql')['user'], $this->cfg('mysql')['password'], $this->cfg('mysql')['db']));
        $this->db->set_charset($this->cfg('mysql')['charset']);
        $this->handle();
    }
    private function cfg($key)
    {
        $data = $this->cfg;
        foreach(explode('.', $key) as $v){
            if(is_array($data) && isset($data[$v])){
                $data = $data[$v];
            }else{
                trigger_error("Unknown config key '$key'", E_USER_ERROR);
            }
        }
        return $data;
    }
    private function query()
    {
        return $this->db ? call_user_func_array( [$this->db, 'query'], func_get_args()) : false;
    }
    private function keyboard($buttons, $options = [])
    {
        if(!isset($options['one_time_keyboard'])){
            $options['one_time_keyboard'] = true;
        }
        if(!isset($options['resize_keyboard'])){
            $options['resize_keyboard'] = false;
        }
        $options['keyboard'] = $buttons;
        return json_encode($options);
    }
    private function getUser()
    {
        if($this->chatId){
            $user = $this->query("SELECT * FROM users WHERE chat_id=?", $this->chatId)->fetch_assoc();
            if($user){
                $user['data'] = json_decode($user['data'], true);
                if(!is_array($user['data'])){
                    $user['data'] = [];
                }
                $this->user = $user;
            }else{
                if($this->query("INSERT INTO users (chat_id,username,created) VALUES (?,?,?)", $this->chatId, $this->message['chat']['username'], time())){
                    $this->getUser();
                }
            }
        }
    }
    private function buttons($arr, $keys = true)
    {
        $arr = array_map(function($item){
            return ["text" => $item];
        }, $keys ? array_keys($arr) : array_values($arr));
        return $this->keyboard([$arr]);
    }
    private function finishQuiz()
    {
        $quiz = $this->cfg('quiz');
        $data = $this->data();
        $rows = [];
        $post = [];
        $name = '';
        $phone = '';
        $ru = isset($data['question1']) && $data['question1'] == array_keys($quiz['question1']['buttons'])[0];
        if($ru){
            $name = isset($data['question5']) ? $data['question5'] : '';
            $phone = isset($data['question7']) ? $data['question7'] : '';
        }else{
            $name = isset($data['question13']) ? $data['question13'] : '';
            $phone = isset($data['question15']) ? $data['question15'] : '';
        }
        if(mb_strlen($phone) && mb_strlen($name)){
            $this->query("UPDATE users SET phone=?,name=? WHERE chat_id=?", $phone, $name, $this->chatId);
        }else{
            $name = $this->user['name'];
            $phone = $this->user['phone'];
            if($ru){
                $data['question5'] = $name;
                $data['question7'] = $phone;
            }else{
                $data['question13'] = $name;
                $data['question15'] = $phone;
            }
        }
        foreach($data as $k => $v){
            if(mb_strlen($v)){
                $kk = $quiz[$k]['text'];
                $rows[$kk] = $v;
                $post[] = $kk . ': ' . $v;
            }
        }
        $chatId = $this->chatId;
        $this->getUser();
        $insertId = $this->query("INSERT INTO history (chat_id, quiz, created) VALUES (?, ?, ?)", $this->chatId, json_encode($rows, JSON_UNESCAPED_UNICODE), time());
        $post[0] = "ID: " . $insertId;
        $this->chatId = $this->cfg('channelId');
        $this->send(implode("\n", $post));
        $this->chatId = $chatId;
        $this->data([]);
    }
    private function question($questionKey)
    {
        $quiz = $this->cfg('quiz');
        if(isset($quiz[$questionKey])){
            $question = $quiz[$questionKey];
            if(mb_strpos($questionKey, "finish") === 0){
                $this->finishQuiz();
                $firstQuestion = array_key_first($quiz);
                $this->data($firstQuestion, '');
                $firstQuestion = $quiz[$firstQuestion];
                return $this->send($quiz[$questionKey]['text'], $this->buttons($firstQuestion['buttons']));
            }
            if(!isset($question['buttons'])){
                $this->send($question['text'], $this->keyboard([], ['remove_keyboard' => true]));
            }else{
                $this->send($question['text'], $this->buttons($question['buttons']));
            }
            $this->data($questionKey, '');
        }
    }
    private function data($key = null, $value = null)
    {
        if($key === null){
            return $this->user['data'];
        }
        $data = $this->user['data'];
        if(!is_array($data)){
            $data = [];
        }
        if(is_scalar($key)){
            $data[$key] = $value;
        }elseif(is_array($key)){
            $data = $key;
        }
        $this->user['data'] = $data;
        $this->query("UPDATE users SET data=? WHERE chat_id=?", json_encode($data, JSON_UNESCAPED_UNICODE), $this->chatId);
        return $data;
    }
    private function startQuiz($text)
    {
        $this->getUser();
        if($text == '/start'){
            $this->data([]);
            $text = 'Choose an option';
        }
        $quiz = $this->cfg('quiz');
        $data = $this->data();
        $lastQuestion = array_key_last($data);
        if($lastQuestion === null){
            $this->question('question1');
        }else{
            if(!mb_strlen($data[$lastQuestion])){
                if($lastQuestion == $text){
                    $this->question($lastQuestion);
                }else{
                    if(isset($quiz[$lastQuestion]['buttons'])){
                        if(isset($quiz[$lastQuestion]['buttons'][$text])){
                            $this->data($lastQuestion, $text);
                            $this->question($quiz[$lastQuestion]['buttons'][$text]);
                        }else{
                            $this->question($lastQuestion);
                        }
                    }else{
                        $this->data($lastQuestion, $text);
                        $this->question($quiz[$lastQuestion]['input']);
                    }
                }
            }
        }
    }
    private function handle()
    {
        $webHook = $this->tg->getWebhookInfo();
        if(!strlen($webHook->url) && isset($_SERVER['HTTP_HOST'])){
            $this->tg->setWebhook([
                'url' => $this->cfg('webhook')
            ]);
        }
        $update = $this->tg->getWebhookUpdates();
        if($update){
            $this->update = $update;
            $this->message = $update->message;
            if($this->message){
                $this->chatId = $this->message['chat']['id'];
                $this->messageId = $this->message['message_id'];
                $this->startQuiz($this->message['text']);
            }
        }
    }
    public function log($data)
    {
        file_put_contents(__DIR__ . '/1.log', print_r($data, true) . "\r\n", FILE_APPEND);
    }
    public function send($text, $keyboard = null)
    {
        $params = [
            'chat_id' => $this->chatId,
            'text' => $text
        ];
        if($keyboard !== null){
            $params['reply_markup'] = $keyboard;
        }
        return $this->tg->sendMessage($params);
    }
}
$bot = new Bot([
    'token' => '6023935245:AAGVwWAAB4onGBC9wHy4W7Z4i-2AdlqzzKs',
    'webhook' => 'https://trucklink.cc/telegram/hrtruckingsolution_bot.php',
    'mysql' => [
        'host' => 'localhost',
        'user' => 'trucklink',
        'password' => 'd2M1ZePFpn',
        'db' => 'bot1',
        'charset' => 'utf8mb4'
    ],
    "channelId" => '-1001811038149',
    "quiz" => [
        "question1" => [
            "text" => "Choose an option",
            "buttons" => [
                "Заявка на водителя" => "question2",
                "Fill out an application" => "question10"
            ]
        ],
        "question2" => [
            "text" => "Вы ранее сотрудничали с нами?",
            "buttons" => [
                "Да" => "question3",
                "Нет" => "question5"
            ]
        ],
        "question3" => [
            "text" => "Сколько водителей вам необходимо?",
            "input" => "question4"
        ],
        "question4" => [
            "text" => "Как называется ваша компания?",
            "input" => "finish1"
        ],
        "finish1" => [
            "text" => "Спасибо, менеджер перезвонит вам для уточнения деталей."
        ],
        "question5" => [
            "text" => "Напишите ваше имя",
            "input" => "question6"
        ],
        "question6" => [
            "text" => "Название вашей компании",
            "input" => "question7"
        ],
        "question7" => [
            "text" => "Ваш номер телефона",
            "input" => "question8"
        ],
        "question8" => [
            "text" => "Сколько требуется водителей на сегодня?",
            "input" => "question9"
        ],
        "question9" => [
            "text" => "Сколько водителей будет нужно в течение 1 мес?",
            "input" => "finish2"
        ],
        "finish2" => [
            "text" => "Приняли вашу заявку, в течение суток с вами свяжется наш менеджер."
        ],
        "question10" => [
            "text" => "Have you already worked with our team?",
            "buttons" => [
                "Yes" => "question11",
                "No" => "question13"
            ]
        ],
        "question11" => [
            "text" => "How many drivers do you need?",
            "input" => "question12"
        ],
        "question12" => [
            "text" => "What is your company name?",
            "input" => "finish3"
        ],
        "finish3" => [
            "text" => "Thank you. Our manager will call you soon."
        ],
        "question13" => [
            "text" => "What is your name?",
            "input" => "question14"
        ],
        "question14" => [
            "text" => "What is your company name?",
            "input" => "question15"
        ],
        "question15" => [
            "text" => "Text your cell phone, please",
            "input" => "question16"
        ],
        "question16" => [
            "text" => "How many drivers do you need today?",
            "input" => "question17"
        ],
        "question17" => [
            "text" => "How many drivers will you need that month?",
            "input" => "finish4"
        ],
        "finish4" => [
            "text" => "Thank you. We accepted your application, our manager will call you soon."
        ]
    ]
]);