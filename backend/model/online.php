<?php
class Online {
    public static function set(int $user_id, int $is_online) {
        global $db;

        return $db->query("UPDATE `user` SET `is_online` = '$is_online', `last_online` = CURRENT_TIMESTAMP WHERE `user_id` = '$user_id'");
    }
}