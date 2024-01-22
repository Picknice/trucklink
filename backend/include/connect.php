<?php
global $db, $dbClass;
$dbData = [
    'host' => 'localhost',
    'username' => 'trucklink',
    'password' => 'd2M1ZePFpn',
    'db' => 'trucklink_db',
    'charset' => 'utf8mb4'
];
if($db){
    unset($db);
}
if($dbClass){
    unset($dbClass);
}
$db = new mysqli($dbData['host'], $dbData['username'], $dbData['password'], $dbData['db']);
if(isset($dbData['charset'])) {
    $db->set_charset($dbData['charset']);
}
if(!$db){
    die('Не работает база данных');
}
$dbClass = new Db($db);