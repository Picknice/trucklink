<?php
error_reporting(E_ALL);
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
    private $update, $message, $messageId, $chatId, $user, $states, $langs, $question2name;
    public function __construct($cfg)
    {
        $this->question2name = [
            "qc_1" => "t-1",
            "vCategory" => "t-2",
            "vc3a" => "t-3",
            "vc2a" => "t-3",
            "vc1a" => "t-3",
            "vc3a1a1" => "t-4",
            "vc3a2a1" => "t-5",
            "vc3a2a1a" => "t-6",
            "fullname" => "t-7",
            "phone" => "t-8",
            "english" => "t-9",
            "documents" => "t-10",
            "driverLicense" => "t-11",
            "prod-phone" => "t-8",
            "prod-document" => "t-12",
            "prod-company" => "t-5",
            "prod-exp" => "t-13",
            "office-phone" => "t-8",
            "office-age" => "t-14",
            "office-english" => "t-9",
            "office-exp" => "t-13",
            "driverExp" => "t-15"
        ];
        $this->langs = [ 'en', 'ru' ];
        $this->states = [];
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
            $options['one_time_keyboard'] = false;
        }
        if(!isset($options['resize_keyboard'])){
            $options['resize_keyboard'] = true;
        }
        $options['keyboard'] = $buttons;
        return json_encode($options);
    }
    private function getUser()
    {
        if($this->chatId){
            $user = $this->query("SELECT * FROM users WHERE tg_id=?", $this->chatId)->fetch_assoc();
            if($user){
                $this->user = $user;
            }else{
                if($this->query("INSERT INTO users (tg_id,states) VALUES (?,?)", $this->chatId, '')){
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
    private function getStates()
    {
        $states = [];
        if($this->user){
            $user = $this->query("SELECT * FROM users WHERE tg_id=?", $this->chatId)->fetch_assoc();
            if($user){
                $this->user = $user;
                $states = @unserialize($user['states']);
                $states = is_array($states) ? $states : [];
            }
        }
        if(!count($states)){
            $states = [
                'service' => '',
                'list' => []
            ];
        }
        $this->states = $states;
        return $states;
    }
    public function saveStates()
    {
        if($this->user){
            return $this->query("UPDATE users SET states=? WHERE tg_id=?", serialize($this->states), $this->chatId);
        }
        return false;
    }
    private function start()
    {
        $this->getUser();
        $this->states = [];
        $this->saveStates();
        $this->tg->sendPhoto([
            'chat_id' => $this->chatId,
            'photo' => Telegram\Bot\FileUpload\InputFile::create(__DIR__ . '/logo.png'),
            'caption' => $this->text('about'),
            'reply_markup' => $this->mainMenu()
        ]);
    }
    private function mainMenu()
    {
        return $this->keyboard([
            [ [ "text" => $this->text('btn_vacancies') ] ],
            [ [ "text" => $this->text('btn_about') ] ],
            [ [ "text" => $this->text('btn_contacts') ] ],
            [ [ "text" => $this->text('btn_comments') ], [ "text" => $this->text('btn_lang') ] ]
        ]);
    }
    private function lang($lang)
    {
        if(isset($this->langs[$lang]) && $this->user){
            if($this->query("UPDATE users SET lang=? WHERE tg_id=?", $lang, $this->chatId)){
                $this->user['lang'] = $lang;
            }
            $this->send($this->text('lang'), $this->mainMenu());
        }
    }
    private function back()
    {
        $service = $this->states['service'];
        if($this->states && $service){
            if(count($this->states['list'])){
                $lastQuestion = array_key_last($this->states['list']);
                $quiz = $this->cfg($this->states['quiz']);
                $lastQuestionType = $quiz[$lastQuestion];
                array_pop($this->states['list']);
                if(count($this->states['list'])){
                    array_pop($this->states['list']);
                }
                $this->saveStates();
                if(!count($this->states['list'])){
                    $this->start();
                }else{
                    if(method_exists($this, $service)){
                        call_user_func([$this, $service]);
                        $this->saveStates();
                    }
                }
            }else{
                $this->start();
            }
        }
    }
    private function about()
    {
        $this->tg->sendPhoto([
            'chat_id' => $this->chatId,
            'photo' => Telegram\Bot\FileUpload\InputFile::create(__DIR__ . '/logo.png'),
            'caption' => $this->text('about')
        ]);
    }
    private function contacts()
    {
        /*
        $this->tg->sendLocation([
            'chat_id' => $this->chatId,
            'longitude' => 69.274041,
            'latitude' => 41.297330
        ]); */
        $this->send($this->text('contacts'));
    }
    private function comments()
    {
        if(!$this->states['service']){
            $this->states['service'] = __FUNCTION__;
            $this->states['quiz'] = 'quizComments';
        }
        $this->quiz('quizComments');
        $this->saveStates();
    }
    private function checkType($type, $keyboard)
    {
        if($type == 'uploadvideo'){
            if(isset($this->message['video'])){
                $video = $this->message['video'];
                return $video['file_id'];
            }
            $this->send($this->text('needUploadVideo'));
            return false;
        }
        $text = isset($this->message['text']) ? $this->message['text'] : '';
        if(!mb_strlen($text)){
            $this->send($this->text('noEmptyType'), $keyboard);
            return false;
        }
        if(is_array($type)){
            $buttons = $this->getButtonsByType($type);
            if(isset($buttons[$text])){
                $button = $buttons[$text];
                if(!is_array($button)){
                    $button = [
                        'question' => '',
                        'text' => is_array($button) && isset($button['text']) ? $button['text'] : ''
                    ];
                }
                if(mb_strlen($button['text'])){
                    $this->send($this->text($button['text']), $keyboard);
                }
                return $text;
            }else{
                $this->send($this->text('noPressButton'), $keyboard);
                return false;
            }
        }
        switch($type){
            case 'text':
                return $text;
            break;
            case 'fullname':
                $fullname = explode(" ", $text);
                if(count($fullname) == 2 && mb_strlen($fullname[0]) && mb_strlen($fullname[1])){
                    return $text;
                }
                $this->send($this->text('incorrectFullname'), $keyboard);
                return false;
            break;
            case 'age':
                $age = intval($text);
                if($age <= 0 || $age > 100){
                    $this->send($this->text('incorrectAge'), $keyboard);
                    return false;
                }
                if($age < 18){
                    $this->send($this->text('incorrectAge18'), $keyboard);
                    return false;
                }
                return $age;
            break;
            case 'birthday':
                if($text === date('d.m.Y', strtotime($text))){
                    return $text;
                }else{
                    $this->send($this->text('incorrectBirthday'), $keyboard);
                    return false;
                }
            break;
            case 'phone':
                if(mb_strlen($text) == 12 && mb_substr($text, 0, 2) === "+1" && mb_substr($text, 2) === preg_replace('/[^0-9]/', '', mb_substr($text, 2))){
                    return $text;
                }else{
                    $this->send($this->text('incorrectPhone'), $keyboard);
                    return false;
                }
            break;
        }
    }
    private function vacanciesFinish($data)
    {
        /*
        $videoKey = $this->text('qv_9');
        if(isset($data[$videoKey])){
            $this->tg->sendVideo([
                'chat_id' => $this->cfg('channelId'),
                'video' => $data[$videoKey]
            ]);
            unset($data[$videoKey]);
        } */
        $list = [];
        foreach($data as $k => $v){
            $k = trim($k);
            $list[] = $k . ': ' . $v;
        }
        $this->tg->sendMessage([
            'chat_id' => $this->cfg('channelId'),
            'text' => implode("\n", $list)
        ]);
    }
    private function commentsFinish($data)
    {
        $list = [];
        foreach($data as $k => $v){
            $k = trim($k);
            $list[] = $k . ': ' . $v;
        }
        $this->tg->sendMessage([
            'chat_id' => $this->cfg('channelId'),
            'text' => implode("\n", $list)
        ]);
    }
    private function getButtonsByType($type)
    {
        $buttons = [];
        if(is_array($type)){
            foreach($type as $v => $t){
                if(is_numeric($v)){
                    if(is_array($t)){
                        foreach($t as $m => $n){
                            if(is_numeric($m)){
                                $buttons[$this->text($n)] = $n;
                            }else{
                                $buttons[$this->text($m)] = $n;
                            }
                        }
                    }
                }elseif(is_string($v)){
                    $buttons[$this->text($v)] = $t;
                }
            }
        }
        return $buttons;
    }
    private function getKeyboardByType($type, $keyboard)
    {
        if(is_array($type) && $keyboard !== null){
            $buttonsRows = [];
            foreach($type as $v => $t){
                if(is_string($v)){
                    $buttonsRows[] = [ [ 'text' => $this->text($v) ] ];
                }elseif(is_string($t)){
                    $buttonsRows[] = [ [ 'text' => $this->text($t) ] ];
                }elseif(is_array($t)){
                    $buttons = [];
                    foreach ($t as $m => $n){
                        if(is_string($m)){
                            $buttons[] = [ 'text' => $this->text($m) ];
                        }elseif(is_string($n)){
                            $buttons[] = [ 'text' => $this->text($n) ];
                        }
                    }
                    $buttonsRows[] = $buttons;
                }
            }
            $keyboard = array_merge($buttonsRows, $keyboard);
        }
        return $keyboard !== null ? $this->keyboard($keyboard) : null;
    }
    private function quiz($quizName)
    {
        $text = isset($this->message['text']) ? $this->message['text'] : '';
        $quiz = $this->cfg($quizName);
        $keyboards = [
            'quizComments' => [ [ [ 'text' => $this->text('btn_back') ] ] ],
            'quizVacancies' => [ [ [ 'text' => $this->text('btn_mainmenu') ], [ 'text' => $this->text('btn_back') ] ] ]
        ];
        $keyboard = isset($keyboards[$quizName]) ? $keyboards[$quizName] : [];
        if(is_array($quiz)){
            $question = count($this->states['list']) ? array_key_last($this->states['list']) : false;
            $question = $question === false ? array_key_first($quiz) : $question;
            $type = isset($quiz[$question]) ? $quiz[$question] : false;
            if($type){
                if(!isset($this->states['list'][$question])){
                    $this->states['list'][$question] = '';
                    $this->send($this->text($question), $this->getKeyboardByType($type, $keyboard));
                }elseif($this->states['list'][$question] !== ''){
                    $value = $this->states['list'][$question];
                    $questions = array_keys($quiz);
                    $nextQuestion = $questions[array_search($question, $questions)+1];
                    if(is_array($type)){
                        $buttons = $this->getButtonsByType($type);
                        $button = isset($buttons[$value]) ? $buttons[$value] : false;
                        if($button && is_array($button) && isset($button['question']) && mb_strlen($button['question'])){
                            $nextQuestion = $button['question'];
                        }
                    }
                    $nextType = $quiz[$nextQuestion];
                    if($nextType == 'finish'){
                        $data = [];
                        foreach($this->states['list'] as $k => $v){
                            if(isset($this->question2name[$k])){
                                $data[$this->text($this->question2name[$k])] = $v;
                            }else{
                                $data[$this->text($k)] = $v;
                            }
                        }
                        $this->states = [];
                        $this->saveStates();
                        $this->send($this->text($nextQuestion), $this->mainMenu());
                        if(method_exists($this, $nextQuestion)) {
                            call_user_func([$this, $nextQuestion], $data);
                        }
                        return true;
                    };
                    $this->states['list'][$nextQuestion] = '';
                    $this->send($this->text($nextQuestion), $this->getKeyboardByType($nextType, $keyboard));
                }else{
                    $value = $this->checkType($type, $this->getKeyboardByType($type, $keyboard));
                    if($value !== false){
                        $this->states['list'][$question] = $value;
                        $this->quiz($quizName);
                    }
                }
            }
        }
    }
    private function vacancies()
    {
        if(!$this->states['service']) {
            $this->states['service'] = __FUNCTION__;
            $this->states['quiz'] = 'quizVacancies';
        }
        $this->quiz('quizVacancies');
    }
    private function checkStates()
    {
        $service = isset($this->states['service']) ? $this->states['service'] : false;
        if($service && method_exists($this, $service)){
            call_user_func([$this, $service]);
            $this->saveStates();
            return true;
        }
        return false;
    }
    private function message($message)
    {
        if($message == '/start' || $message == $this->text('btn_mainmenu')){
            return $this->start();
        }
        $this->getStates();
        if($message == $this->text('btn_back')){
            return $this->back();
        }
        if($this->checkStates()){
            return true;
        }
        $this->states = [];
        $this->saveStates();
        $this->getStates();
        if($message == $this->text('btn_lang', 'en')){
            $this->lang(1);
        }
        if($message == $this->text('btn_lang', 'ru')){
            $this->lang(0);
        }
        if($message == $this->text('btn_about')){
            $this->about();
        }
        if($message == $this->text('btn_contacts')){
            $this->contacts();
        }
        if($message == $this->text('btn_comments')){
            $this->comments();
        }
        if($message == $this->text('btn_vacancies')){
            $this->vacancies();
        }
        $this->saveStates();
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
                $this->getUser();
                $this->message($text = isset($this->message['text']) ? $this->message['text'] : '');
            }
        }
    }
    public function text($name, $lang = false)
    {
        if($lang === false) {
            $lang = $this->user ? intval($this->user['lang']) : 0;
        }else{
            $lang = array_search($lang, $this->langs);
            if($lang === false){
                $lang = 0;
            }
        }
        if(isset($this->langs[$lang])){
            $value = $this->cfg($name);
            $lang = $this->langs[$lang];
            if(is_array($value) && isset($value[$lang])){
                return $value[$lang];
            }
        }
        return $name;
    }
    public function log($data)
    {
        file_put_contents(__DIR__ . '/1.log', print_r($data, true) . "\r\n", FILE_APPEND);
    }
    public function send($text, $keyboard = null)
    {
        if(!is_scalar($text)){
            $text = print_r($text, true);
        }
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
try {
    $bot = new Bot([
        'token' => '5841699475:AAE9zAuv1hax9EkaN53x_EDcTfbJKsiT6vY',
        'webhook' => 'https://trucklink.cc/telegram/HRcaravan_bot.php',
        'mysql' => [
            'host' => 'localhost',
            'user' => 'trucklink',
            'password' => 'd2M1ZePFpn',
            'db' => 'hretl_bot',
            'charset' => 'utf8mb4'
        ],
        'channelId' => '-1001904517733',
        'start' => [
            'en' => "Caravan Freight offer transportation, logistics, and brokerage services to help businesses grow and increase profitability.\nWith 10+ years of experience, we provide reliable quality services and enterprise-class software solutions. Let us help your business reach new heights.",
            'ru' => "Caravan Freight предлагает транспортные, логистические и брокерские услуги, помогающие бизнесу расти и повышать прибыльность.\nБлагодаря более чем 10-летнему опыту мы предоставляем надежные качественные услуги и программные решения корпоративного класса. Позвольте нам помочь вашему бизнесу достичь новых высот."
        ],
        'btn_mainmenu' => [
            'en' => "🏠 Main menu",
            'ru' => "🏠 Главное меню"
        ],
        'btn_about' => [
            'en' => "🏢 About us",
            'ru' => "🏢 О нас"
        ],
        'btn_vacancies' => [
            'en' => "💼 Vacancies",
            'ru' => "💼 Вакансии"
        ],
        'btn_contacts' => [
            'en' => "☎️ Contacts",
            'ru' => "☎️ Контакты"
        ],
        'btn_comments' => [
            'en' => "💬 Comments and suggestions",
            'ru' => "💬 Комментарии и предложения"
        ],
        'btn_lang' => [
            'en' => "🇷🇺 Рус",
            'ru' => "🇬🇧 Eng"
        ],
        'lang' => [
            'en' => "Selected 🇬🇧 Eng",
            'ru' => "Выбран 🇷🇺 Рус"
        ],
        'about' => [
            'en' => "Caravan Freight offer transportation, logistics, and brokerage services to help businesses grow and increase profitability.\nWith 10+ years of experience, we provide reliable quality services and enterprise-class software solutions. Let us help your business reach new heights.",
            'ru' => "Caravan Freight предлагает транспортные, логистические и брокерские услуги, помогающие бизнесу расти и повышать прибыльность.\nБлагодаря более чем 10-летнему опыту мы предоставляем надежные качественные услуги и программные решения корпоративного класса. Позвольте нам помочь вашему бизнесу достичь новых высот."
        ],
        'contacts' => [
            'en' => "info@etlgroupllc.com\n+1 570 314 4444",
            'ru' => "info@etlgroupllc.com\n+1 570 314 4444"
        ],
        'qc_1' => [
            'en' => "Write to us here, we will definitely answer.",
            'ru' => "Напишите нам сюда, мы обязательно ответим."
        ],
        'btn_back' => [
            'en' => "🔙 Back",
            'ru' => "🔙 Назад"
        ],
        'commentsFinish' => [
            'en' => "Thank you, we will reply as soon as possible!",
            'ru' => "Спасибо, в ближайшее время ответим!"
        ],
        'noEmptyType' => [
            'en' => "Incorrect answer",
            'ru' => "Некорректный ответ"
        ],
        'noPressButton' => [
            'en' => "Press button",
            'ru' => "Нажмите кнопку"
        ],
        'quizComments' => [
            "fullname" => "fullname",
            "phone" => "phone",
            "qc_1" => "text",
            "commentsFinish" => "finish"
        ],
        'quizVacancies' => [
            "vCategory" => [
                "vc3" => [
                    "question" => "vc3a",
                    "text" => "vacanciesBegin"
                ],
                "vc2" => [
                    "question" => "vc2a",
                    "text" => "vacanciesBegin"
                ],
                "vc1" => [
                    "question" => "vc1a",
                    "text" => "vacanciesBegin"
                ]
            ],
            "vc3a" => [
                "vc3a3" => [
                    "question" => "vc3a2a1",
                    "text" => "vc3a3a"
                ],
                "vc3a2" => [
                    "question" => "vc3a2a1",
                    "text" => "vc3a2a"
                ],
                "vc3a1" => [
                    "question" => "vc3a1a1",
                    "text" => "vc3a1a"
                ]
            ],
            "vc3a1a1" => [
                "vc3a1a1a" => [
                    "question" => "fullname",
                    "text" => ""
                ],
                "vc3a1a1b" => [
                    "question" => "fullname",
                    "text" => ""
                ],
                "vc3a1a1c" => [
                    "question" => "fullname",
                    "text" => ""
                ]
            ],
            "vc3a2a1" => [
                "yes" => [
                    "question" => "fullname",
                    "text" => ""
                ],
                "no" => [
                    "question" => "vc3a2a1a",
                    "text" => ""
                ]
            ],
            "vc3a2a1a" => [
                "yes" => [
                    "question" => "fullname",
                    "text" => "",
                ],
                "no" => [
                    "question" => "fullname",
                    "text" => ""
                ]
            ],
            "vc2a" => [
                "vc2a1" => [
                    "question" => "prod-phone",
                    "text" => "vc2a1a"
                ],
                "vc2a2" => [
                    "question" => "prod-phone",
                    "text" => "vc2a2a"
                ],
                "vc2a3" => [
                    "question" => "prod-phone",
                    "text" => "vc2a3a"
                ]
            ],
            "vc1a" => [
                "vc1a1" => [
                    "question" => "office-phone",
                    "text" => "vc1a1a"
                ],
                "vc1a2" => [
                    "question" => "office-phone",
                    "text" => "vc1a2a"
                ]
            ],
            "fullname" => "fullname",
            "phone" => "phone",
            "english" => [
                [ "english-1", "english-2" ],
                [ "english-3", "english-4" ]
            ],
            "documents" => [
                [ "document-1", "document-2" ],
                [ "document-3", "document-4" ],
            ],
            "driverExp" => [
                [ "driver-exp-1", "driver-exp-2" ],
                [ "driver-exp-3", "driver-exp-4" ]
            ],
            "driverLicense" => [
                "yes" => [
                    "question" => "vacanciesFinish",
                    "text" => ""
                ],
                "no" => [
                    "question" => "vacanciesFinish",
                    "text" => ""
                ]
            ],
            "prod-phone" => "phone",
            "prod-document" => [
                "yes" => "",
                "no" => ""
            ],
            "prod-company" => [
               "yes" => "",
               "no" => ""
            ],
            "prod-exp" => [
                "yes" => [
                    "question" => "vacanciesFinish",
                    "text" => ""
                ],
                "no" => [
                    "question" => "vacanciesFinish",
                    "text" => ""
                ]
            ],
            "office-phone" => "phone",
            "office-age" => "age",
            "office-english" => [
                [ "english-1", "english-2" ],
                [ "english-3", "english-4" ]
            ],
            "office-exp" => [
                "yes" => [
                    "question" => "vacanciesFinish",
                    "text" => ""
                ],
                "no" => [
                    "question" => "vacanciesFinish",
                    "text" => ""
                ]
            ],
            "vacanciesFinish" => "finish"
        ],
        'vc2a1' => [
            'en' => "Welder",
            'ru' => "Сварщик"
        ],
        'vc2a2' => [
            'en' => "Auto electrician",
            'ru' => "Автоэлектрик"
        ],
        'vc2a3' => [
            'en' => "Mechanic",
            'ru' => "Механик"
        ],
        'vc1a1' => [
            'en' => "Secretary",
            'ru' => "Секретарь"
        ],
        'vc1a2' => [
            'en' => "Manager",
            'ru' => "Управляющий"
        ],
        'vc2a1a' => [
            'en' => "✅ Caravan Freight announces a vacancy for car assembly and maintenance masters ✅\n\n❗️Working conditions:❗️\n\n📌 Flexible work schedule\n📌 Career opportunities\n📌 Young friendly team\n💵 Timely payment + bonuses after the interview",
            'ru' => "✅ Caravan Freight объявляет о вакансии мастеров сборки и обслуживания авто ✅\n\n❗️Условия работы:❗️\n\n📌Гибкий график работы\n📌 Возможность карьерного роста\n📌 Молодой дружный коллектив\n💵 Своевременная оплата + бонусы после собеседования"
        ],
        'vc2a2a' => [
            'en' => "✅ Caravan Freight announces a vacancy for car assembly and maintenance masters ✅\n\n❗️Working conditions:❗️\n\n📌 Flexible work schedule\n📌 Career opportunities\n📌 Young friendly team\n💵 Timely payment + bonuses after the interview",
            'ru' => "✅ Caravan Freight объявляет о вакансии мастеров сборки и обслуживания авто ✅\n\n❗️Условия работы:❗️\n\n📌Гибкий график работы\n📌 Возможность карьерного роста\n📌 Молодой дружный коллектив\n💵 Своевременная оплата + бонусы после собеседования"
        ],
        'vc2a3a' => [
            'en' => "✅ Caravan Freight announces a vacancy for car assembly and maintenance masters ✅\n\n❗️Working conditions:❗️\n\n📌 Flexible work schedule\n📌 Career opportunities\n📌 Young friendly team\n💵 Timely payment + bonuses after the interview",
            'ru' => "✅ Caravan Freight объявляет о вакансии мастеров сборки и обслуживания авто ✅\n\n❗️Условия работы:❗️\n\n📌Гибкий график работы\n📌 Возможность карьерного роста\n📌 Молодой дружный коллектив\n💵 Своевременная оплата + бонусы после собеседования"
        ],
        'noVacancies' => [
            'en' => "No vacancies",
            'ru' => "Нет вакансий"
        ],
        'vacanciesBegin' => [
            'en' => "Let's start filling  your resume",
            'ru' => "Приступим к заполнению вашего резюме"
        ],
        "vCategory" => [
            'en' => "Choose category",
            'ru' => "Выберите категорию"
        ],
        "vc1" => [
            'en' => "Office",
            'ru' => "Офис"
        ],
        "vc2" => [
            'en' => "Production",
            'ru' => "Производство"
        ],
        "vc3" => [
            'en' => "Drivers",
            'ru' => "Водители"
        ],
        "vc3a" => [
            'en' => "Choose one of the vacancies",
            'ru' => "Выберите одну из вакансий"
        ],
        "vc2a" => [
            'en' => "Choose one of the vacancies",
            'ru' => "Выберите одну из вакансий"
        ],
        "vc1a" => [
            'en' => "Choose one of the vacancies",
            'ru' => "Выберите одну из вакансий"
        ],
        "vc3a1" => [
            'en' => "Owner Operator",
            'ru' => "Овнер Оператор"
        ],
        "vc3a2" => [
            'en' => "Driver 26 foot",
            'ru' => "Водитель 26 фут"
        ],
        "vc3a3" => [
            'en' => "Driver 16 foot",
            'ru' => "Водитель 16 фут"
        ],
        "vc3a1a" => [
            'en' => "✅ Caravan Freight announces a vacancy for an Owner operator ✅\n\n💥Requirements:💥\n\n📌 The truck is not older than 2018\n\n❗️Working conditions:❗️\n\n📌Flexible work schedule\n📌 Career opportunity\n📌 Young friendly team\n💵 Timely payment + bonuses",
            'ru' => "✅ Caravan Freight объявляет о вакансии Овнер Оператор ✅\n\n💥Требования:💥\n\n📌 Грузовой автомобиль не старше 2018 года\n\n❗️Условия работы:❗️\n\n📌Гибкий график работы\n📌 Возможность карьерного роста\n📌 Молодой дружный коллектив\n💵 Своевременная оплата + бонусы"
        ],
        "vc3a2a" => [
            'en' => "✅ Caravan Freight announces vacancy Driver 26 ft ✅\n\n💥Requirements:💥\n\n📌 Citizenship or legal status, green card\n📌 Work permit\n📌 American rights\n\n❗️Working conditions:❗️\n\n📌 Flexible work schedule\n📌 Career opportunity\n📌 Young friendly team\n💵 Timely payment + bonuses",
            'ru' => "✅ Caravan Freight объявляет о вакансии Водитель 26 фут ✅\n\n💥Требования:💥\n\n📌  Гражданство или легальный статус, грин карта\n📌  Разрешение на работу\n📌  Права американские\n\n❗️Условия работы:❗️\n\n📌 Гибкий график работы\n📌 Возможность карьерного роста\n📌 Молодой дружный коллектив\n💵 Своевременная оплата + бонусы"
        ],
        "vc3a3a" => [
            'en' => "✅ Caravan Freight announces vacancy Driver 16 ft ✅\n\n💥Requirements:💥\n\n📌 Citizenship or legal status, green card\n📌 Work permit\n📌 American rights\n\n❗️Working conditions:❗️\n\n📌 Flexible work schedule\n📌 Career opportunity\n📌 Young friendly team\n💵 Timely payment + bonuses",
            'ru' => "✅ Caravan Freight объявляет о вакансии Водитель 16 фут ✅\n\n💥Требования:💥\n\n📌  Гражданство или легальный статус, грин карта\n📌  Разрешение на работу\n📌  Права американские\n\n❗️Условия работы:❗️\n\n📌 Гибкий график работы\n📌 Возможность карьерного роста\n📌 Молодой дружный коллектив\n💵 Своевременная оплата + бонусы"
        ],
        "vc1a1a" => [
            'en' => "✅ Caravan Freight announces a job vacancy in the office ✅\n\n❗️Working conditions:❗️\n\n📌Flexible work schedule\n📌 Career opportunities\n📌 Young friendly team\n💵 Timely payment + bonuses after the interview",
            'ru' => "✅ Caravan Freight объявляет о вакансии работ в офисе ✅\n\n❗️Условия работы:❗️\n\n📌Гибкий график работы\n📌 Возможность карьерного роста\n📌 Молодой дружный коллектив\n💵 Своевременная оплата + бонусы после собеседования"
        ],
        "vc1a2a" => [
            'en' => "✅ Caravan Freight announces a job vacancy in the office ✅\n\n❗️Working conditions:❗️\n\n📌Flexible work schedule\n📌 Career opportunities\n📌 Young friendly team\n💵 Timely payment + bonuses after the interview",
            'ru' => "✅ Caravan Freight объявляет о вакансии работ в офисе ✅\n\n❗️Условия работы:❗️\n\n📌Гибкий график работы\n📌 Возможность карьерного роста\n📌 Молодой дружный коллектив\n💵 Своевременная оплата + бонусы после собеседования"
        ],
        "vc1a3a" => [
            'en' => "✅ Caravan Freight announces a job vacancy in the office ✅\n\n❗️Working conditions:❗️\n\n📌Flexible work schedule\n📌 Career opportunities\n📌 Young friendly team\n💵 Timely payment + bonuses after the interview",
            'ru' => "✅ Caravan Freight объявляет о вакансии работ в офисе ✅\n\n❗️Условия работы:❗️\n\n📌Гибкий график работы\n📌 Возможность карьерного роста\n📌 Молодой дружный коллектив\n💵 Своевременная оплата + бонусы после собеседования"
        ],
        "vc3a1a1" => [
            'en' => "What is your car?",
            'ru' => "Какая у вас машина?"
        ],
        "vc3a1a1a" => [
            'en' => "Box truck - 16ft",
            'ru' => "Грузовик 16 фут"
        ],
        "vc3a1a1b" => [
            'en' => "Box truck - 26ft",
            'ru' => "Грузовик 26 фут"
        ],
        "vc3a1a1c" => [
            'en' => "Sprinter Van",
            'ru' => "Спринтер Фургон"
        ],
        "fullname" => [
            'en' => "👤 Enter your full name (First and Last name)",
            'ru' => "👤 Введите свое полное имя (имя и фамилию)"
        ],
        "phone" => [
            'en' => "📱 Enter your contact phone number (example: +1XXXXXXXXXX):",
            'ru' => "📱 Введите свой контактный номер телефона (пример: +1XXXXXXXXXX):"
        ],
        "prod-phone" => [
            'en' => "📱 Enter your contact phone number (example: +1XXXXXXXXXX):",
            'ru' => "📱 Введите свой контактный номер телефона (пример: +1XXXXXXXXXX):"
        ],
        "office-phone" => [
            'en' => "📱 Enter your contact phone number (example: +1XXXXXXXXXX):",
            'ru' => "📱 Введите свой контактный номер телефона (пример: +1XXXXXXXXXX):"
        ],
        "office-age" => [
            'en' => "How old are you?",
            'ru' => "Сколько вам лет?"
        ],
        "prod-document" => [
            'en' => "Do you have a work permit?",
            'ru' => "Есть ли у вас разренение на работу?"
        ],
        "prod-company" => [
            'en' => "Do you have a company?",
            'ru' => "Есть ли у вас компания?"
        ],
        "prod-exp" => [
            'en' => "Do you have work experience?",
            'ru' => "Есть ли у вас опыт работы?"
        ],
        "office-exp" => [
            'en' => "Do you have work experience?",
            'ru' => "Есть ли у вас опыт работы?"
        ],
        "documents" => [
            'en' => "What do you have from this list?",
            'ru' => "Что у вас есть из этого списка?"
        ],
        'driverLicense' => [
            'en' => "Do you have driver's license?",
            'ru' => "Есть ли у вас водительские права?"
        ],
        'incorrectFullname' => [
            'en' => "Incorrect first and last name",
            'ru' => "Некорерректные имя и фамилия"
        ],
        'incorrectPhone' => [
            'en' => "Invalid phone number",
            'ru' => "Неправильный номер телефона"
        ],
        'vacanciesFinish' => [
            'en' => "Thank you, we will contact you shortly!",
            'ru' => "Спасибо, в ближайшее время свяжемся с вами!"
        ],
        'yes' => [
            'en' => "Yes",
            'ru' => "Да",
        ],
        'no' => [
            'en' => "No",
            'ru' => "Нет",
        ],
        "vc3a2a1" => [
            'en' => "Do you have a company?",
            'ru' => "Есть ли у вас компания?"
        ],
        "vc3a2a1a" => [
            'en' => "Are you ready to open it?",
            'ru' => "Готовы ли вы ее открыть?"
        ],
        "english" => [
            'en' => "What is your level of English?",
            'ru' => "Какой у тебя уровень английского?"
        ],
        "office-english" => [
            'en' => "What is your level of English?",
            'ru' => "Какой у тебя уровень английского?"
        ],
        "english-1" => [
            'en' => "Beginner",
            'ru' => "Новичок"
        ],
        "english-2" => [
            'en' => "Intermediate",
            'ru' => "Средний"
        ],
        "english-3" => [
            'en' => "Advanced",
            'ru' => "Продвинутый"
        ],
        "english-4" => [
            'en' => "Fluent",
            'ru' => "Свободно владеющий"
        ],
        "document-1" => [
            'en' => "Work Permit",
            'ru' => "Разрешение на работу"
        ],
        "document-2" => [
            'en' => "Green Card",
            'ru' => "Грин карта"
        ],
        "document-3" => [
            'en' => "US Citizenship",
            'ru' => "Гражданство США"
        ],
        "document-4" => [
            'en' => "There is nothing",
            'ru' => "Ничего нет"
        ],
        "incorrectAge" => [
            'en' => "Incorrect age",
            'ru' => "Некорректный возраст"
        ],
        "incorrectAge18" => [
            'en' => "You must be over 18 years old",
            'ru' => "Вам должно быть больше 18 лет"
        ],
        "driverExp" => [
            'en' => "What is your work experience?",
            'ru' => "Какой у вас опыт работы?"
        ],
        "driver-exp-1" => [
            'en' => "No experience",
            'ru' => "Нет опыта"
        ],
        "driver-exp-2" => [
            'en' => "1-3 years",
            'ru' => "1-3 года"
        ],
        "driver-exp-3" => [
            'en' => "3-5 years",
            'ru' => "3-5 лет"
        ],
        "driver-exp-4" => [
            'en' => "5+ years",
            'ru' => "5+ лет"
        ],
        "t-1" => [
            'en' => "Comment or suggestion",
            'ru' => "Комментарий или предложение"
        ],
        "t-2" => [
            'en' => "Category",
            'ru' => "Категория"
        ],
        "t-3" => [
            'en' => "Vacancy",
            'ru' => "Вакансия"
        ],
        "t-4" => [
            'en' => "Car",
            'ru' => "Машина"
        ],
        "t-5" => [
            'en' => "Have a company",
            'ru' => "Есть компания"
        ],
        "t-6" => [
            'en' => "Ready to start a company",
            'ru' => "Готов открыть компанию"
        ],
        "t-7" => [
            'en' => "First and last name",
            'ru' => "Имя и фамилия"
        ],
        "t-8" => [
            'en' => "Phone",
            'ru' => "Телефон"
        ],
        "t-9" => [
            'en' => "English proficiency",
            'ru' => "Уровень владения английским"
        ],
        "t-10" => [
            'en' => "Document",
            'ru' => "Документ"
        ],
        "t-11" => [
            'en' => "Have a driver's license",
            'ru' => "Есть водительское удостоверение"
        ],
        "t-12" => [
            'en' => "Have a work permit",
            'ru' => "Есть разрешение на работу"
        ],
        "t-13" => [
            'en' => "Have work experience",
            'ru' => "Есть опыт работы"
        ],
        "t-14" => [
            'en' => "Age",
            'ru' => "Возраст"
        ],
        "t-15" => [
            'en' => "Experience",
            'ru' => "Опыт работы"
        ]
    ]);
} catch(Exception $e){
    file_put_contents(__DIR__ . '/1.log', $e->getMessage());
}