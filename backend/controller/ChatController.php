<?php
class ChatController
{
    public static function createMessage($applicationId, $text, $userId = 0)
    {
        $errors = [];
        $application = Application::get($applicationId);
        if(!$application){
            $errors[] = 'Application not selected';
        }
        $creator = 0;
        if($userId){
            $user = User::get($userId);
            if(!$user){
                $errors[] = 'User not found';
            }else{
                if($application['user_id'] != $user['user_id'] && $user['user_type_id'] == 1){
                    $error[] = 'No rights';
                }else {
                    $creator = $application['user_id'] == $user['user_id'] ? 2 : 1;
                }
            }
        }
        if(!strlen(trim($text))){
            $errors[] = 'No text entered';
        }
        if(!empty($errors)){
            return [
                'type' => 'error',
                'message' => array_shift($errors)
            ];
        };
        $chatUsers = Chat::getChatUsers($applicationId);
        if(!in_array($userId, $chatUsers)){
            $chatUsers[] = $userId;
        }
        if(!in_array($application['user_id'], $chatUsers)){
            $chatUsers[] = $application['user_id'];
        }
        if($creator > 0 && $creator != 2){
            if(!Chat::countMessages($applicationId, $userId)){
                $messageId = Chat::createMessage($applicationId, 'join #'. $userId);
                if($messageId) {
                    Transport::sendToUsers($chatUsers, ['applicationId' => $applicationId, 'joined' => UserController::getInfo($userId), 'messageId' => $messageId]);
                }
            }
        }
        $messageId = Chat::createMessage($applicationId, $text, $userId, $userId == 0);

        if($messageId){
            if($creator > 0){
                $messages = Chat::readMessagesFor($applicationId, $application['user_id'], $creator == 2);
                if(is_array($messages)){
                    Transport::sendToUsers($chatUsers, ['applicationId' => $applicationId, 'readed' => $messages]);
                }
            }
            $message = ChatController::getMessage($applicationId, $messageId);
            if($message) {
                Transport::sendToUsers($chatUsers, ['applicationId' => $applicationId, 'message' => $message]);
                $users = $creator === 2 ? Chat::getChatModeratorsAndAdmins($application['application_id']) : [$application['user_id']];
                Transport::sendToUsers($users, [ 'applicationId' => $applicationId, 'users' => $users, 'notify_unread' => true ]);
            }
        }
        return $messageId;
    }
    public static function getMessage($applicationId, $messageId)
    {
        $types = User::getTypes();
        $message = Chat::getMessage($applicationId, $messageId);
        if($message){
            $type = isset($types[$message['user_type_id']]) ? $types[$message['user_type_id']] : false;
            $message['user'] = [
                'id' => $message['user_id'],
                'name' => $message['user_name'],
                'surname' => $message['user_surname'],
                'online' => $message['user_online'],
                'type' => $type ? [
                    'id' => $type['user_type_id'],
                    'name' => $type['name']
                ] : false
            ];
            unsetKeys($message, ['user_name', 'user_surname', 'user_online', 'user_type_id']);
            return $message;
        }
        return false;
    }
    public static function getMessages($applicationId, $userId, $messageId = null)
    {
        $errors = [];
        $application = Application::get($applicationId);
        if(!$application){
            $errors[] = 'Application not selected';
        }
        $user = User::get($userId);
        if(!$user){
            $errors[] = 'User not found';
        }else{
            if($application['user_id'] != $user['user_id'] && $user['user_type_id'] == 1){
                $error[] = 'No rights';
            }
        }
        if(!empty($errors)){
            return [
                'type' => 'error',
                'message' => array_shift($errors)
            ];
        }
        $unreadMessages = Chat::getUnreadMessagesFor($applicationId, $application['user_id'], $user['user_id'] == $application['user_id']);
        if(count($unreadMessages) && Chat::readMessages($application['application_id'], $unreadMessages)){
            Transport::sendToUsers(Chat::getChatUsers($applicationId), ['applicationId' => $application['application_id'], 'readed' => $unreadMessages]);
        }
        $messages = Chat::getMessages($applicationId, $messageId);
        $list = [];
        $types = User::getTypes();
        if(is_array($messages)){
            foreach($messages as $message){
                $type = isset($types[$message['user_type_id']]) ? $types[$message['user_type_id']] : false;
                $message['user'] = [
                    'id' => $message['user_id'],
                    'name' => $message['user_name'],
                    'surname' => $message['user_surname'],
                    'online' => $message['user_online'],
                    'type' => $type ? [
                        'id' => $type['user_type_id'],
                        'name' => $type['name']
                    ] : false,
                    'is_current' => $message['user_id'] == $userId
                ];
                unsetKeys($message, ['user_name', 'user_surname', 'user_online', 'user_type_id']);
                $list[] = $message;
            }
        }
        return $list;
    }
}