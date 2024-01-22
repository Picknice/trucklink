<?php
use Workerman\Worker;
ini_set('memory_limit', '1024M');
require_once __DIR__ . '/require.php';
class ChatWebSocketServer
{
    private $webSocketServer, $localServer, $clients, $users, $client2user;
    public function __construct($webSocketServer, $localServer)
    {
        $chat = $this;
        $this->clients = [];
        $this->users = [];
        $this->client2user = [];
        $this->localServer = new Worker($localServer);
        $this->localServer->count = 1;
        $this->localServer->onMessage = function($connection, $message) use (&$chat)
        {
            $data = @json_decode($message, true);
            if(is_array($data)){
                foreach($data['user_ids'] as $userId) {
                    $chat->sendUser($userId, $data['data']);
                }
            }
            $connection->close();
        };
        $this->localServer->onWorkerStart = function() use (&$chat, $webSocketServer)
        {
            $chat->webSocketServer = new Worker($webSocketServer, [
                'ssl' => [
                    'local_cert'  => '/etc/letsencrypt/live/trucklink.cc/fullchain.pem',
                    'local_pk'    => '/etc/letsencrypt/live/trucklink.cc/privkey.pem',
                    'verify_peer' => false,
                ]
            ]);
            $chat->webSocketServer->count = 1;
            $chat->webSocketServer->transport = 'ssl';
            $chat->webSocketServer->onConnect = function($connection) use (&$chat)
            {
                $chat->connect($connection);
            };
            $chat->webSocketServer->onClose = function($connection) use (&$chat)
            {
                $chat->disconnect($connection);
            };
            $chat->webSocketServer->onMessage = function($connection, $message) use (&$chat)
            {
                $data = @json_decode($message, true);
                require __DIR__ . '/../include/connect.php';
                if(is_array($data)){
                    $chat->message($connection, $data);
                }

            };
            $chat->webSocketServer->listen();
        };
    }
    public function connect($connection)
    {
        if(!isset($this->clients[$connection->id])){
            $this->clients[$connection->id] = $connection;
        }
    }
    public function disconnect($connection)
    {
        if(isset($this->client2user[$connection->id])) {
            $userId = $this->client2user[$connection->id];
            require __DIR__ . '/../include/connect.php';
            db()->query("UPDATE user SET is_online=0 WHERE user_id=?", $userId);
            db()->close();
            if(isset($this->users[$userId][$connection->id])){
                unset($this->users[$userId][$connection->id]);
            }
            if(!count($this->users[$userId])){
                unset($this->users[$userId]);
            }
            unset($this->client2user[$connection->id]);
        }
    }
    public function message($connection, $message)
    {
        if(isset($message['sessionToken'])){
            $userSession = UserSession::check($message['sessionToken']);
            if($userSession){
                $userId = intval($userSession['user_id']);
                db()->query("UPDATE user SET last_online=?, is_online=? WHERE user_id=?", date('Y-m-d H:i:s'), 1, $userId);
                if(!isset($this->users[$userId])){
                    $this->users[$userId] = [];
                }
                if(!isset($this->users[$userId][$connection->id])){
                    $this->users[$userId][$connection->id] = 1;
                }
                if(!isset($this->client2user[$connection->id])){
                    $this->client2user[$connection->id] = $userId;
                }
                db()->close();
            }
        }
    }
    public function send($connectionId, $data)
    {
        if(isset($this->clients[$connectionId])){
            $this->clients[$connectionId]->send(json_encode($data));
        }
    }
    public function sendUser($userId, $data)
    {
        if(isset($this->users[$userId])){
            foreach($this->users[$userId] as $connectionId => $v){
                $this->send($connectionId, $data);
            }
        }
    }
}
new ChatWebSocketServer(WS_LOCAL_SERVER, WS_TRANSPORT_SERVER);
Worker::runAll();