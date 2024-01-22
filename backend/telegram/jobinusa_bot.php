<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');
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
        if(isset($_GET['b'])){
            foreach($this->query("SELECT * FROM tg_channels")->fetch_all(MYSQLI_ASSOC) as $channel){
                if($channel['to_id'] !== 1647915624){
                    continue;
                }
                $this->chatId = '-100'. $channel['to_id'];
                try {
                    $res = $this->send('По вопросам сотрудничества и добавления объявлений', $this->ikeyboard([
                        [
                            [
                                'text' => 'Добавить объявление',
                                'url' => 'https://t.me/usajobbot'
                            ]
                        ]
                    ]));
                }catch(Exception $e){
                    var_dump($e->getMessage());
                }
                pre($res);
            }
            die('ok');
        }
        //$this->chatId = 233663991;
        //$this->getUser();
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
            $user = $this->query("SELECT * FROM tg_bot_users WHERE chat_id=?", $this->chatId)->fetch_assoc();
            if($user){
                $user['data'] = json_decode($user['data'], true);
                if(!is_array($user['data'])){
                    $user['data'] = [];
                }
                $this->user = $user;
            }else{
                if($this->query("INSERT INTO tg_bot_users (chat_id,username,created) VALUES (?,?,?)", $this->chatId, $this->message['chat']['username'], time())){
                    $this->getUser();
                }
            }
        }
    }
    private function buttons($arr, $keys = true, $mode = true)
    {
        if(count($arr) > 2){
            $mode = false;
        }
        $arr = array_map(function($item) use (&$mode){
            $button = ["text" => $item, "callback_data" => $item];
            if($mode){
                return $button;
            }
            return [$button];
        }, $keys ? array_keys($arr) : array_values($arr));
        return $this->ikeyboard($mode ? [$arr] : $arr);
    }
    private function finishQuiz()
    {
        $quiz = $this->cfg('quiz');
        $data = $this->data();
        $rows = [];
        $post = [];
        foreach($data as $k => $v){
            if(mb_strlen($v)){
                $question = $quiz[$k];
                $kk = isset($question['text2']) ? $question['text2'] : $question['text'];
                if(mb_strpos($kk, " тип ") !== false){
                    $kk = 'Тип';
                }else{
                    preg_match('/<b>(.*?)<\/b>/ui', $kk, $m);
                    if(count($m) == 2){
                        $kk = mb_strtoupper(mb_substr($m[1], 0, 1)) . mb_strtolower(mb_substr($m[1], 1));
                    }
                }
                $rows[$kk] = $v;
                $post[] = $kk . ': <b>' . $v . '</b>';
            }
        }
        $chatId = $this->chatId;
        $this->getUser();
        $photo = $this->user['photo'] ? $this->user['photo'] : '';
        $insertId = $this->query("INSERT INTO tg_bot_posts (chat_id, data, created, photo) VALUES (?, ?, ?, ?)", $this->chatId, json_encode($rows, JSON_UNESCAPED_UNICODE), time(), $photo);
        $this->query("UPDATE tg_bot_users SET photo=? WHERE chat_id=?", '', $this->chatId);
        $post[0] = "ID: " . $insertId;
        $this->chatId = $this->cfg('channelId');
        $buttons = [
            [[ "text" => $this->cfg('admin_text_1'), "callback_data" => "admin_set_status_0" ]]
        ];
        if(isset($rows['Тип']) && mb_strpos($rows['Тип'], 'Консультация') !== false){
            $buttons[] = [[ "text" => $this->cfg('admin_text_2'), "callback_data" => "admin_set_status_4" ]];
        }else{
            $buttons[] = [[ "text" => $this->cfg('admin_text_4'), "callback_data" => "admin_set_status_2" ]];
        }
        $searchCity = false;
        if(isset($rows['Город'])){
            $searchCity = $rows['Город'];
            $cities = $this->query("SELECT to_id,name FROM tg_channels")->fetch_all(MYSQLI_ASSOC);
            $cities = is_array($cities) ? array_column($cities, "name", "to_id") : [];
            $a = mb_substr(str_replace("-", " ", $searchCity), 0, -1);
            foreach($cities as $id => $name){
                $city = mb_substr($name, mb_strpos($name, " в ") + 3);
                $b = mb_substr($city, 0, -1);
                if($a == $b || mb_strpos($b, $a) !== false){
                    $searchCity = [
                        'name' => str_replace(" ", "-", $city),
                        'original' => $rows['Город'],
                        'channel' => $id
                    ];
                    break;
                }
            }
            if($searchCity && $searchCity['name'] !== 'США'){
                $buttons[] = [[ "text" => $this->cfg('admin_text_5') . $searchCity['name'], "callback_data" => "admin_set_status_3" ]];
            }
        }
        if($photo){
            $res = $this->tg->sendPhoto([
                'chat_id' => $this->chatId,
                'photo' => $photo,
                'caption' => implode("\n", $post),
                'parse_mode' => 'html',
                'reply_markup' => $this->ikeyboard($buttons)
            ]);
        }else{
            $res = $this->send(implode("\n", $post), $this->ikeyboard($buttons));
        }
        $this->chatId = $chatId;
        if($res){
            $this->query("UPDATE tg_bot_posts SET moderation_id=?,city=? WHERE id=?", $res->message_id, $searchCity ? json_encode($searchCity, JSON_UNESCAPED_UNICODE) : '', $insertId);
            $this->data([]);
        }
    }
    private function question($questionKey)
    {
        $quiz = $this->cfg('quiz');
        if(isset($quiz[$questionKey])){
            $question = $quiz[$questionKey];
            if(mb_strpos($questionKey, "finish") === 0){
                $this->finishQuiz();
                $this->send($quiz[$questionKey]['text']);
                return $this->commandStart();
            }
            $mainText = $question['text'];
            if(isset($question['text2'])){
                $this->send($mainText);
                $mainText = $question['text2'];
            }
            if(isset($this->message['photo'])){
                $photo = array_pop($this->message['photo']);
                if(is_array($photo)){
                    $this->query("UPDATE tg_bot_users SET photo=? WHERE chat_id=?", $photo['file_id'], $this->chatId);
                }
            }
            $this->data($questionKey, '');
            if(!isset($question['buttons'])){
                $this->send($mainText);
            }else{
                $this->send($mainText, $this->buttons($question['buttons']));
            }
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
        $this->query("UPDATE tg_bot_users SET data=? WHERE chat_id=?", json_encode($data, JSON_UNESCAPED_UNICODE), $this->chatId);
        return $data;
    }
    private function startQuiz($text = '')
    {
        if($text == ''){
            $this->data([]);
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
    private function ikeyboard($buttons)
    {
        $options['inline_keyboard'] = $buttons;
        return json_encode($options);
    }
    private function commandStart()
    {
        $this->data([]);
        $this->send('Добро пожаловать!', $this->keyboard([[
            [ 'text' => 'Добавить объявление', 'callback_data' => 'command_start_yes' ]
        ]]));
        $this->send($this->cfg('text1'), $this->ikeyboard([
            [
                [ "text" => "Да", "callback_data" => "command_start_yes" ],
                [ "text" => "Нет", "callback_data" => "command_start_no" ]
            ]
        ]));
    }
    private function deleteMessage($chatId, $messageId)
    {
        return $this->tg->deleteMessage([
            'chat_id' => $chatId,
            'message_id' => $messageId
        ]);
    }
    private function callback($data){
        switch($data){
            case 'admin_set_status_0':
            case 'admin_set_status_2':
            case 'admin_set_status_3':
            case 'admin_set_status_4':
                $row = $this->query("SELECT * FROM tg_bot_posts WHERE moderation_id=?", $this->messageId)->fetch_assoc();
                if($row){
                    $chatId = $this->chatId;
                    $this->chatId = $row['chat_id'];
                    $cities = $this->query("SELECT to_id,name FROM tg_channels")->fetch_all(MYSQLI_ASSOC);
                    $cities = is_array($cities) ? array_column($cities, "name", "to_id") : [];
                    $posted = false;
                    switch($data){
                        case 'admin_set_status_0':
                            $this->send($this->cfg('admin_result_1'));
                            $this->deleteMessage($chatId, $this->messageId);
                            break;
                        case 'admin_set_status_2':
                            $posted = true;
                            break;
                        case 'admin_set_status_3':
                            $posted = true;
                            $city = @json_decode($row['city'], true);
                            if($city){
                                $cities = [ $city['channel'] => "Работа в ". $city['name'] ];
                            }else{
                                $cities = [ '1856388917' => "Работа в США" ];
                            }
                            break;
                        case 'admin_set_status_4':
                            $this->send($this->cfg('admin_result_2'));
                            $this->deleteMessage($chatId, $this->messageId);
                            break;
                    }
                    if($posted){
                        $postData = @json_decode($row['data'], true);
                        if(isset($postData['Описание']) && isset($postData['Имя']) && isset($postData['Номер телефона'])){
                            $this->send($this->cfg('admin_result_3'));
                            $post = $postData["Описание"];
                            $post .= "\n\n";
                            $post .= "Name: <b>" . $postData['Имя'] . "</b>\nPhone: <a href=\"tel:" . $postData['Номер телефона'] . "\"><b>".$postData['Номер телефона']."</b></a>";
                            foreach($cities as $id => $city) {
                                $tmpChatId = $this->chatId;
                                $this->chatId = '-100' . $id;
                                try {
                                    if($row['photo']){
                                        $this->tg->sendPhoto([
                                           'chat_id' => $this->chatId,
                                           'photo' => $row['photo'],
                                           'caption' => $post,
                                           'parse_mode' => 'html'
                                        ]);
                                    }else {
                                        $this->send($post);
                                    }
                                } catch( Exception $e ){

                                }
                                $this->chatId = $tmpChatId;
                            }
                            $this->deleteMessage($chatId, $this->messageId);
                        }
                    }
                }
                return;
                break;
            case 'command_start_yes':
                $this->startQuiz();
                break;
            case 'command_start_no':
                $this->send($this->cfg('text2'), $this->ikeyboard([
                    [
                        ["text" => $this->cfg('text3'), "url" => $this->cfg('link3') ]
                    ]
                ]));
                break;
            default:
                $this->startQuiz($data);
                break;
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
            if($this->update->callback_query){
                $this->message = $this->update->callback_query->message;
            }else{
                $this->message = $update->message;
            }
            if($this->message){
                $this->chatId = $this->message['chat']['id'];
                $this->messageId = $this->message['message_id'];
                if($this->chatId > 0) {
                    $this->getUser();
                }
                if($this->update->callback_query){
                    return $this->callback($this->update->callback_query->data);
                }
                $text = isset($this->message['photo']) ? $this->message['caption'] : $this->message['text'];
                if($this->chatId < 0){
                    return;
                }
                switch($text){
                    case '/start':
                        $this->commandStart();
                        break;
                    default:
                        $this->startQuiz($text);
                        break;
                }
            }
        }
    }
    public function log($data)
    {
        file_put_contents(__DIR__ . '/2.log', print_r($data, true) . "\r\n", FILE_APPEND);
    }
    public function send($text, $keyboard = null, $params = [])
    {
        $params['chat_id'] = $this->chatId;
        $params['text'] = $text;
        $params['parse_mode'] = 'html';
        if($keyboard !== null){
            $params['reply_markup'] = $keyboard;
        }
        return $this->tg->sendMessage($params);
    }
}
$bot = new Bot([
    'token' => '6042204437:AAF3Yt5kDZcWJlFJ85rLgGtFGZbQEo5yXfw',
    'webhook' => 'https://trucklink.cc/telegram/jobinusa_bot.php',
    'mysql' => [
        'host' => 'localhost',
        'user' => 'trucklink',
        'password' => 'd2M1ZePFpn',
        'db' => 'trucklink_db',
        'charset' => 'utf8mb4'
    ],
    "channelId" => '-1001805920244',
    "text1" => "Хотите подать обьявление?",
    "text2" => "Официальная сеть Telegram каналов",
    "text3" => "🔥 Работа в США 🔥",
    "link3" => "https://t.me/jobinusa",
    "admin_text_1" => "Отклонить",
    "admin_text_2" => "Выполнено",
    "admin_text_4" => "Пост везде",
    "admin_text_5" => "Пост в ",
    "admin_result_1" => "Заявка отклонена",
    "admin_result_2" => "Заявка решена",
    "admin_result_3" => "Ваше объявление принято",
    "quiz" => [
        "question1" => [
            "text" => "Выберите тип обьявления: 👇",
            "buttons" => [
                "Ищу работу 🧑‍💻" => "question20",
                "Ищу сотрудников 💼" => "question30",
                "Реклама моих услуг 📈" => "question40",
                "Сниму/Сдам/Продам Жилье 🏡" => "question50",
                "Консультация ☎️" => "question60",
                "Платное размещение 💵" => "question70",
            ]
        ],
        "question20" => [
            "text" => "Напишите следующую информацию и наш менеджер с вами свяжеться 😎",
            "text2" => "Напишите <b>ОПИСАНИЕ</b> вашего объявления:",
            "input" => "question21"
        ],
        "question21" => [
            "text" => "Напишите ваш <b>НОМЕР ТЕЛЕФОНА</b> 📲:",
            "input" => "finish22"
        ],
        "finish22" => [
            "text" => "Спасибо. Ваш запрос отправлен на обработку. Ждите звонка от менеджера! 😎"
        ],
        "question30" => [
            "text" => "Напишите следующую информацию и наш менеджер с вами свяжеться 😎",
            "text2" => "Напишите <b>ОПИСАНИЕ</b> вашего объявления:",
            "input" => "question31"
        ],
        "question31" => [
            "text" => "Выберите <b>ГОРОД</b> объявления:",
            "buttons" => [
                "Чикаго" => "question32",
                "Бостон" => "question32",
                "Нью-Йорк" => "question32",
                "Сакраменто" => "question32",
                "Филадельфия" => "question32",
                "Нью-Джерси" => "question32",
                "Лос-Анджелес" => "question32",
                "Майами" => "question32",
                "Сан-Франциско" => "question32",
                "США" => "question32"
            ]
        ],
        "question32" => [
            "text" => "Напишите ваше <b>ИМЯ</b>:",
            "input" => "question33"
        ],
        "question33" => [
            "text" => "Напишите ваш <b>НОМЕР ТЕЛЕФОНА</b> 📲:",
            "input" => "finish34"
        ],
        "finish34" => [
            "text" => "Спасибо. Ваш запрос отправлен на обработку. Ждите звонка от менеджера! 😎"
        ],
        "question40" => [
            "text" => "Напишите следующую информацию и наш менеджер с вами свяжеться 😎",
            "text2" => "Напишите <b>ОПИСАНИЕ</b> вашего объявления:",
            "input" => "question41"
        ],
        "question41" => [
            "text" => "Выберите <b>ГОРОД</b> объявления:",
            "buttons" => [
                "Чикаго" => "question42",
                "Бостон" => "question42",
                "Нью-Йорк" => "question42",
                "Сакраменто" => "question42",
                "Филадельфия" => "question42",
                "Нью-Джерси" => "question42",
                "Лос-Анджелес" => "question42",
                "Майами" => "question42",
                "Сан-Франциско" => "question42",
                "США" => "question42"
            ]
        ],
        "question42" => [
            "text" => "Напишите ваше <b>ИМЯ</b>:",
            "input" => "question43"
        ],
        "question43" => [
            "text" => "Напишите ваш <b>НОМЕР ТЕЛЕФОНА</b> 📲:",
            "input" => "finish44"
        ],
        "finish44" => [
            "text" => "Спасибо. Ваш запрос отправлен на обработку. Ждите звонка от менеджера! 😎"
        ],
        "question50" => [
            "text" => "Напишите следующую информацию и наш менеджер с вами свяжеться 😎",
            "text2" => "Напишите <b>ОПИСАНИЕ</b> вашего объявления (Адрес / город):",
            "input" => "question51"
        ],
        "question51" => [
            "text" => "Выберите <b>ГОРОД</b> объявления:",
            "buttons" => [
                "Чикаго" => "question52",
                "Бостон" => "question52",
                "Нью-Йорк" => "question52",
                "Сакраменто" => "question52",
                "Филадельфия" => "question52",
                "Нью-Джерси" => "question52",
                "Лос-Анджелес" => "question52",
                "Майами" => "question52",
                "Сан-Франциско" => "question52",
                "США" => "question52"
            ]
        ],
        "question52" => [
            "text" => "Напишите ваше <b>ИМЯ</b>:",
            "input" => "question53"
        ],
        "question53" => [
            "text" => "Напишите ваш <b>НОМЕР ТЕЛЕФОНА</b> 📲:",
            "input" => "finish54"
        ],
        "finish54" => [
            "text" => "Спасибо. Ваш запрос отправлен на обработку. Ждите звонка от менеджера! 😎"
        ],
        "question60" => [
            "text" => "Напишите следующую информацию и наш менеджер с вами свяжеться 😎",
            "text2" => "Напишите <b>ОПИСАНИЕ</b> вашей проблемы:",
            "input" => "question61"
        ],
        "question61" => [
            "text" => "Напишите ваше <b>ИМЯ</b>:",
            "input" => "question62"
        ],
        "question62" => [
            "text" => "Напишите ваш <b>НОМЕР ТЕЛЕФОНА</b> 📲:",
            "input" => "finish63"
        ],
        "finish63" => [
            "text" => "Спасибо. Ваш запрос отправлен на обработку. Ждите звонка от менеджера! 😎"
        ],
        "question70" => [
            "text" => "Напишите следующую информацию и наш менеджер с вами свяжеться 😎",
            "text2" => "Напишите <b>ОПИСАНИЕ</b> вашего объявления:",
            "input" => "question71"
        ],
        "question71" => [
            "text" => "Напишите ваше <b>ИМЯ</b>:",
            "input" => "question72"
        ],
        "question72" => [
            "text" => "Напишите ваш <b>НОМЕР ТЕЛЕФОНА</b> 📲:",
            "input" => "finish73"
        ],
        "finish73" => [
            "text" => "Спасибо. Ваш запрос отправлен на обработку. Ждите звонка от менеджера! 😎"
        ],
    ]
]);