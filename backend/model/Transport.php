<?php
class Transport
{
    public static function sendToUsers(array $users, $data)
    {
        $client = @stream_socket_client(WS_TRANSPORT_SERVER);
        if($client){
            @fwrite($client, json_encode([
                'user_ids' => $users,
                'data' => $data
            ]));
            @fclose($client);
        }
    }
    public static function send($userId, $data)
    {
        self::sendToUsers([$userId], $data);
    }
}