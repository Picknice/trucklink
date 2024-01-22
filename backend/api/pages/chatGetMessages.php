<?php
$user = user();
$application = Application::get(intval(param('application_id')));
if(!$application){
    return apiError('No application');
}
$messageId = intval(param('message_id'));
return ChatController::getMessages($application['application_id'], $user['user_id'], $messageId > 0 ? $messageId : null);