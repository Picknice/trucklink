<?php
$user = user();
$application = Application::get(intval(param('application_id')));
if(!$application){
    return apiError('No application');
}
$messages = Chat::getUnreadMessagesFor($application['application_id'], $application['user_id'], $user['user_id'] == $application['user_id']);
if(count($messages) && Chat::readMessages($application['application_id'], $messages)){
    Transport::sendToUsers(Chat::getChatUsers($application['application_id']), [ 'applicationId' => $application['application_id'], 'readed' => $messages ]);
}
return true;