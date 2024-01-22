<?php

$path_page = __DIR__ . '/../api/pages';

switch ($uri_api_type) {
    case '/application':
        $path_page .= '/application.php';
        break;
    case '/user_avatar':
        $path_page .= '/user_avatar.php';
        break;
    case '/chat_get_messages':
        $path_page .= '/chatGetMessages.php';
        break;
    case '/chat_send_message':
        $path_page .= '/chatSendMessage.php';
        break;
    case '/chat_read_messages':
        $path_page .= '/chatReadMessages.php';
        break;
    case '/admin_application':
        $path_page .= '/adminApplication.php';
        break;
    case '/admin_application_status':
        $path_page .= '/adminApplicationStatus.php';
        break;
    case '/admin_application_remove':
        $path_page .= '/adminApplicationRemove.php';
        break;
    default:
        $path_page .= '/index.php';
}

if (!file_exists($path_page)) {
    $path_page = __DIR__ . '/../api/pages/index.php';
}
apiResult(require_once $path_page);