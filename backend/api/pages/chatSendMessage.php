<?php
$user = user();
$applicationId = intval(param('application_id'));
$message = param('message');
if(!mb_strlen($message)){
    return apiError('Enter message');
}
return ChatController::createMessage($applicationId, $message, $user['user_id']);