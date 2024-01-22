<?php
class UserSessionController {
    public static function check($token) {
        $data = UserSession::check($token);

        $user_id = $data['user_id'];

        $user = getDbDate('user', 'user_id', $user_id)->fetch_assoc();

        if (!$user) {
            return;
        }

        $_SESSION['user'] = $user;
    }
}
if(!isset($_SESSION['user']) && !isset($_SESSION['user']['user_id']) && isset($_COOKIE['session_token'])){
    UserSessionController::check($_COOKIE['session_token']);
}