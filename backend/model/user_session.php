<?php
class UserSession {
    public static function create($token, $user_id) {
        global $db;

        return $db->query("INSERT INTO `user_session`(`token`, `user_id`) VALUES ('$token', '$user_id')");
    }

    public static function check($token) {
        global $db;
        $data = $db->query("SELECT * FROM `user_session` WHERE `token` = '$token'");
        return $data->fetch_assoc();
    }
}