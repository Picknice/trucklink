<?php

switch ($http_method) {
    case 'POST':
        UserController::updateAvatar($_SESSION['user']['user_id'], $_FILES['avatar']);
        die;
        break;
}