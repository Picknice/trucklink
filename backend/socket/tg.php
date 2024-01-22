<?php
error_reporting(0);
ini_set('display_errors', 'on');
set_time_limit(0);
ini_set('memory_limit', '1000M');
require_once dirname(__DIR__) . '/mtproto/vendor/autoload.php';
require_once __DIR__ . '/require.php';
class TGParser
{
    public $mt, $updateChannelsTime = 24 * 3600, $vkGroups, $lockFile;
    private function botApi($method, $params = [])
    {
        try {
            return json_decode(file_get_contents('https://api.telegram.org/bot' . PARSER_BOT_API . '/' . $method . '?' . http_build_query($params)), true);
        } catch (Exception $e){

        }
        return false;
    }
    public function __construct()
    {
        $this->vkGroups = [];
        $groups = $this->vkapi('groups.get', [
            'extended' => 1
        ]);
        $cities = [];
        if(is_array($groups)){
            foreach($groups['items'] as $item){
                $name = explode(" в ", $item['name']);
                if(count($name) == 2){
                    $cityKey = str_replace('-', ' ', $name[1]);
                    if(!isset($cities[$cityKey])) {
                        $cities[$cityKey] = [];
                    }
                    $cities[$cityKey][$name[0]] = $item['id'];
                }
            }
        }
        $tgChannels = db("SELECT * FROM tg_channels")->fetch_all(MYSQLI_ASSOC);
        $tgChannelsList = [];
        foreach($tgChannels as $item){
            $name = explode(" в ", $item['name']);
            if(count($name) == 2){
                $item['city_name'] = $name[1];
                $cityKey = str_replace('-', ' ', $item['city_name']);
                if(!isset($tgChannelsList[$cityKey])){
                    $tgChannelsList[$cityKey] = $item;
                }
            }
        }
        $noSearch = [];
        foreach($tgChannelsList as $city => $item){
            if(!isset($cities[$city])){
                $noSearch[] = $city;
            }else{
                $this->vkGroups[$item['to_id']] = $cities[$city];
            }
        }
        $MadelineProto = new \danog\MadelineProto\API(__DIR__ . '/session.madeline');
        $MadelineProto->start();
        $MadelineProto->async(false);
        $me = $MadelineProto->getSelf();
        if(!$me['bot']){
            $this->mt = $MadelineProto;
            $this->handle();
        }
        $MadelineProto->echo('OK, done!');
    }
    public function addButton()
    {
        foreach( db()->query("SELECT * FROM tg_channels") as $channel){
            pre($channel);

        }
    }
    public function wallPost($tgChannelId, $message, $photo = false)
    {
        $vkGroups = isset($this->vkGroups[$tgChannelId]) ? $this->vkGroups[$tgChannelId] : [];
        foreach($vkGroups as $category => $id){
            $params = [
                'owner_id' => '-' . $id,
                'from_group' => 1,
                'message' => strip_tags($message)
            ];
            if($photo && file_exists($photo)){
                $uploadServer = $this->vkapi('photos.getWallUploadServer', [
                    'group_id' => $id
                ]);
                if(is_array($uploadServer)){
                    $curl = curl_init($uploadServer['upload_url']);
                    curl_setopt($curl, CURLOPT_POST, 1);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_HEADER, false);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, [
                       'photo' => curl_file_create($photo)
                    ]);
                    $uploadPhoto = curl_exec($curl);
                    if($uploadPhoto){
                        $uploadPhoto = json_decode($uploadPhoto, true);
                        if(is_array($uploadPhoto)){
                            $photoSave = $this->vkapi('photos.saveWallPhoto', [
                                'group_id' => $id,
                                'photo' => stripslashes($uploadPhoto['photo']),
                                'server' => $uploadPhoto['server'],
                                'hash' => $uploadPhoto['hash']
                            ]);
                            if(is_array($photoSave) && count($photoSave)){
                                $params['attachments'] = 'photo' . $photoSave[0]['owner_id'] . '_' . $photoSave[0]['id'];
                            }
                        }
                    }
                }
            }
            $res = $this->vkapi('wall.post', $params);
            if(is_array($res) && isset($res['post_id'])){
                $this->vkapi('wall.post', [
                    'owner_id' => '-' . $id,
                    'post_id' => $res['post_id']
                ]);
            }
        }
    }
    public function vkapi($method, array $params = [])
    {
        if(!isset($params['access_token'])){
            $params['access_token'] = VKAPI['token'];
        }
        if(!isset($params['v'])){
            $params['v'] = VKAPI['v'];
        }
        usleep(300000);
        $result = @file_get_contents("https://api.vk.com/method/$method" . (count($params) ? '?' . http_build_query($params) : ''));
        $result = @json_decode($result, true);
        if(isset($result['response'])){
            return $result['response'];
        }
        return false;
    }
    public function handle()
    {
        $channels = [];
        $needUpdate = [];
        foreach(db()->query("SELECT * FROM tg_channels")->fetch_all(MYSQLI_ASSOC) as $channel){
            if($channel['last_update'] > time() - $this->updateChannelsTime){
                $channels[] = $channel;
            }else{
                $needUpdate[] = $channel;
            }
        }
        if(count($needUpdate)){
            $this->updateChannels($needUpdate);
        }
        $toChannels = [];
        foreach($channels as $channel){
            $toId = $channel['to_id'];
            if($toId && mb_strlen($channel['name'])){
                $toChannels[$toId] = $channel['name'];
            }
        }
        $channels = is_array($channels) ? array_column($channels, null, 'from_id') : [];
        $updateDialogs = [];
        $buttonsChats = [];
        $dialogs = $this->mt->getFullDialogs();
        foreach($dialogs as $dialogId => $dialog){
            if(isset($dialog['peer']['channel_id'])) {
                $channelId = $dialog['peer']['channel_id'];
                if(isset($channels[$channelId]) ){
                    $channel = $channels[$channelId];
                    if($channel['buttons'] != 0){
                        $buttonsChats[$dialogId] = $channel;
                    }
                    if(!$channel['message_id']){
                        db()->query("UPDATE tg_channels SET message_id=? WHERE id=?", $dialog['top_message'], $channel['id']);
                    }elseif($channel['message_id'] != $dialog['top_message']) {
                        $updateDialogs[$dialogId] = $channel;
                    }
                }
            }
        }
        $this->updateDialogs($updateDialogs, $toChannels);
        $this->updateButtons($buttonsChats, $channels);
    }
    public function updateButtons($dialogs, $channels)
    {
        $buttons = [];
        foreach($channels as $channel){
            if($channel['buttons'] == 0 && mb_strlen($channel['name']) && mb_strlen($channel['url'])){
                $buttons[] = [ [
                    'text' => $channel['name'],
                    'url' => $channel['url']
                ] ];
            }
        }
        if(count($buttons)){
            foreach($dialogs as $channel){
                $keyboard = json_encode([ 'inline_keyboard' => $buttons]);
                $hash = md5($keyboard);
                if($channel['hash'] != $hash) {
                    $res = $this->botApi('deleteMessage', [
                        'chat_id' => '-100' . $channel['to_id'],
                        'message_id' => $channel['buttons']
                    ]);
                    $result = $this->botApi('sendMessage', [
                        'chat_id' => '-100' . $channel['to_id'],
                        'text' => "📌 Навигация по КАНАЛУ 📌\nНеобходимый город с актуальными вакансиями найдёте здесь 👇",
                        'reply_markup' => $keyboard
                    ]);
                    if (is_array($result) && isset($result['result'])) {
                        db()->query("UPDATE tg_channels SET hash=?, buttons=? WHERE id=?", $hash, $result['result']['message_id'], $channel['id']);
                    }
                }
            }
        }
    }
    public function updateDialogs($dialogs, $toChannels)
    {
        foreach($dialogs as $dialogId => $channel){
            $toSendChannels = [];
            if($channel['to_id']){
                $toSendChannels[] = $channel['to_id'];
            }else{
                $toSendChannels = array_keys($toChannels);
            }
            $messages = $this->mt->messages->getHistory(['peer' => [ '_' => 'peerChannel', 'channel_id' => $channel['from_id']], 'min_id' => $channel['message_id'], 'limit' => 100]);
            if(is_array($messages) && count($messages['messages'])){
                foreach(array_reverse($messages['messages']) as $message){
                    foreach($toSendChannels as $toId) {
                        $message['message'] = str_replace( [
                            "\nПерейти к объявлению"
                        ], '', $message['message']);
                        $message['message'] = str_replace("\n👉 ВСЕ ОБЪЯВЛЕНИЯ НА RUSREK.COM", "\n👉 ВСЕ ОБЪЯВЛЕНИЯ НА DOSKAUSA.COM", $message['message']);
                        $message['message'] = str_replace("\n👉 ПОДПИСАТЬСЯ @rusrek_com", "", $message['message']);
                        $message['message'] = str_replace("🤖 НАШ БОТ @rusrekbot_bot", "🤖 НАШ БОТ https://t.me/usajobbot", $message['message']);
                        $textHash = md5($messages['message']);
                        if($channel['text_hash'] == $textHash){
                            continue;
                        }
                        try {
                            $sendMessage = [
                                'peer' => ['_' => 'peerChannel', 'channel_id' => $toId],
                                'message' => $message['message']
                            ];
                            if (isset($message['entries'])) {
                                $sendMessage['entries'] = $message['entries'];
                            }
                            $photoFile = __DIR__ . '/tg-' . uniqid() . '.jpg';
                            $hasPhoto = false;
                            if (isset($message['media']) && $message['media']['_'] === 'messageMediaPhoto') {
                                $sendMessage['media'] = $message['media'];
                                $this->mt->downloadToFile($message['media'], $photoFile);
                                $sendMessage['media'] = $this->mt->messages->uploadMedia([
                                    'peer' => ['_' => 'peerChannel', 'channel_id' => $toId],
                                    'media' => [
                                        '_' => 'inputMediaUploadedPhoto',
                                        'file' => $photoFile
                                    ]
                                ]);
                                $hasPhoto = true;
                                $result = $this->mt->messages->sendMedia($sendMessage);
                            } else {
                                $result = $this->mt->messages->sendMessage($sendMessage);
                            }
                            $this->wallPost($toId, $message['message'], $hasPhoto ? $photoFile : false);
                            if (file_exists($photoFile)) {
                                @unlink($photoFile);
                            }
                            db()->query("UPDATE tg_channels SET message_id=?,text_hash=? WHERE id=?", $message['id'], $textHash, $channel['id']);
                            usleep(300000);
                        } catch (Exception $e) {
                        }
                    }
                }
            }
        }
    }
    public function updateChannels($channels)
    {
        $toChannels = [];
        $notFoundFrom = [];
        $notFoundTo = [];
        $tgChannels = $this->getChannels();
        foreach($channels as $channel){
            $title = $channel['name'];
            $fromId = $channel['from_id'];
            $toId = $channel['to_id'];
            if($fromId){
                if(!isset($tgChannels[$fromId])){
                    $notFoundFrom[$fromId] = true;
                }else{
                    if($toId){
                        if(!isset($tgChannels[$toId])){
                            $notFoundTo[$toId] = true;
                        }else{
                            $title = trim(preg_replace('/[^А-яёъь\s\-]/ui', '', $tgChannels[$toId]['title']));
                        }
                    }
                }
            }
            db("UPDATE tg_channels SET name=?,last_update=? WHERE id=?", $title, time(), $channel['id']);
        }
        return [
            'from' => $notFoundFrom,
            'to' => $notFoundTo
        ];
    }
    public function getChannels()
    {
        $dialogs = $this->mt->getFullDialogs();
        $channels = [];
        if(is_array($dialogs)){
            foreach($dialogs as $id => $dialog){
                if($dialog['peer']['_'] == 'peerChannel'){
                    $channels[] = $id;
                }
            }
        }
        if(count($channels)){
            $channels = $this->mt->channels->getChannels([
                'id' => $channels
            ]);
            if(is_array($channels) && isset($channels['chats'])) {
                foreach ($channels['chats'] as $channel) {
                    if ($channel['megagroup'] || $channel['gigagroup']) {
                        continue;
                    }
                    if ($channel['_'] !== 'channel') {
                        continue;
                    }
                    $channels[$channel['id']] = $channel;
                }
            }
        }
        return $channels;
    }
}
$tg = new TGParser;