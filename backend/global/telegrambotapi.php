<?php
class TelegramBotApi
{
    private $token;
    public function __construct($token)
    {
        $this->token = $token;
    }
    private function sendPhoto($chatId, $file, $message = null)
    {
        if(!file_exists($file)){
            return false;
        }
        $params = [
            'chat_id' => $chatId,
            'photo' => curl_file_create(realpath($file))
        ];
        if($message !== null){
            $params['caption'] = $message;
            $params['parse_mode'] = 'html';
        }
        $result = $this->request('sendPhoto', $params, [
            'Content-Type: multipart/form-data'
        ]);
        if($result && isset($result['result'])){
            return $result['result']['message_id'];
        }
        return false;
    }
    public function send($userId, $message, $photo = false)
    {
        if($photo){
            return $this->sendPhoto($userId, $photo, $message);
        }
        $result = $this->request('sendMessage', [
            'chat_id' => $userId,
            'text' => $message,
            'parse_mode' => 'html'
        ]);
        if($result && isset($result['result'])){
            return $result['result']['message_id'];
        }
        return false;
    }
    private function request($method, array $params = [], $headers = [])
    {
        $curl = curl_init('https://api.telegram.org/bot' . $this->token . '/' . $method);
        if(count($headers)){
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        $data = curl_exec($curl);
        curl_close($curl);
        if($data){
            return @json_decode($data, true);
        }
        return $data;
    }
}