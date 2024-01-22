<?php
class Chat
{
    public static function createMessage($applicationId, $text, $userId = 0, $read = false)
    {
        return db()->query("INSERT INTO messages (application_id,user_id,text,created,readed) VALUES (?,?,?,?,?)", $applicationId, $userId, $text, time(), $read ? 1 : 0);
    }
    public static function getMessage($applicationId, $messageId)
    {
        return db()->query("SELECT messages.*, user.name as user_name, user.surname as user_surname, user.is_online as user_online, user.user_type_id as user_type_id FROM messages LEFT JOIN user ON messages.user_id = user.user_id WHERE application_id=? AND id=?", $applicationId, $messageId)->fetch_assoc();
    }
    public static function readMessages($applicationId, $messages = [])
    {
        return db()->query("UPDATE messages SET readed = 1 WHERE application_id=? AND id IN (".implode(",", $messages).")", $applicationId);
    }
    public static function getUnreadMessages($applicationId, $messageId = null)
    {
        $messages = db()->query("SELECT id FROM messages WHERE application_id=?".($messageId != null ? ' AND id <= ' . intval($messageId) : '')." AND readed=0", $applicationId)->fetch_all(MYSQLI_ASSOC);
        return is_array($messages) ? array_column($messages, 'id') : [];
    }
    public static function readMessagesFor($applicationId, $userId, $reverse = false)
    {
        $messages = self::getUnreadMessagesFor($applicationId, $userId, $reverse);
        return count($messages) && self::readMessages($applicationId, $messages);
    }
    public static function getUnreadMessagesFor($applicationId, $userId, $reverse = false)
    {
        $applicationId = intval($applicationId);
        $userId = intval($userId);
        if($applicationId && $userId) {
            $messages = db()->query("SELECT id FROM messages WHERE application_id = ? AND readed = 0 AND user_id" . ($reverse ? ' <> ' : ' = ') . $userId, $applicationId)->fetch_all(MYSQLI_ASSOC);
            return is_array($messages) ? array_column($messages, 'id') : [];
        }
        return [];
    }
    public static function getMessages($applicationId, $messageId = null, $count = 10)
    {
        return db()->query("SELECT messages.*, user.name as user_name, user.surname as user_surname, user.is_online as user_online, user.user_type_id as user_type_id FROM messages LEFT JOIN user ON messages.user_id = user.user_id WHERE application_id=?" . ($messageId === null ? '' : ' AND id < ' . intval($messageId) ) . " ORDER BY id DESC LIMIT ". intval($count), $applicationId)->fetch_all(MYSQLI_ASSOC);
    }
    public static function getChatUsers($applicationId, $noCreator = false)
    {
        $application = Application::get(intval($applicationId));
        if($application) {
            $users = db()->query("SELECT user_id FROM messages WHERE application_id=? and user_id <> 0 GROUP BY user_id", $applicationId)->fetch_all(MYSQLI_ASSOC);
            $users = is_array($users) ? array_column($users, null, 'user_id') : [];
            if(!isset($users[$application['user_id']])) {
                $application['user_id'] = 1;
            }
            if($noCreator && isset($users[$application['user_id']])){
                unset($users[$application['user_id']]);
            }
            return array_keys($users);
        }
        return [];
    }
    public static function getChatModeratorsAndAdmins($applicationId)
    {
        return self::getChatUsers($applicationId, true);
    }
    public static function countMessages($applicationId, $userId = false)
    {
        $countMessages = db()->query("SELECT COUNT(id) as count FROM messages WHERE application_id = ?" . ($userId !== false ? " AND user_id = " . intval($userId) : ''), $applicationId)->fetch_assoc();
        return is_array($countMessages) ? intval($countMessages['count']) : 0;
    }
}