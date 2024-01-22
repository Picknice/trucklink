<?php
class User {
    public static function create($email, $telephone, $password, $name, $surname) {
        global $db;

        $query = $db->query("INSERT INTO `user` (`email`, `telephone`, `password`, `name`, `surname`) VALUES ('$email', '$telephone', '$password', '$name', '$surname')");

        if ($query) {
            return mysqli_insert_id($db);
        }
    }
    public static function get($userId)
    {
        return db()->query("SELECT user.*, user_type.name as user_type_name, user_type.power as user_type_power FROM user LEFT JOIN user_type ON user.user_type_id = user_type.user_type_id WHERE user_id=?", $userId)->fetch_assoc();
    }
    public static function getTypes()
    {
        $types = db()->query("SELECT * FROM user_type")->fetch_all(MYSQLI_ASSOC);
        return is_array($types) ? array_column($types, null, 'user_type_id') : [];
    }
    public static function update($userId, $companyName, $companyHours, $companyCity, $companyAddress, $companyZip, $telephone, $name, $surname, $email, $card, $card_name, $card_date, $card_cvv, $profile_verify){
        $updates = [];
        if($companyName){
            $updates['company_name=?'] = $companyName;
        }
        if($companyHours){
            $updates['company_hours=?'] = $companyHours;
        }
        if($companyCity){
            $updates['company_city=?'] = $companyCity;
        }
        if($companyAddress){
            $updates['company_address=?'] = $companyAddress;
        }
        if($companyZip){
            $updates['company_zip=?'] = $companyZip;
        }
        if($telephone){
            $updates['telephone=?'] = $telephone;
        }
        if($name){
            $updates['name=?'] = $name;
        }
        if($surname){
            $updates['surname=?'] = $surname;
        }
        if($email){
            $updates['email=?'] = $email;
        }
        if($card){
            $updates['card=?'] = $card;
        }
        if($card_name){
            $updates['card_name=?'] = $card_name;
        }
        if($card_date){
            $updates['card_date=?'] = $card_date;
        }
        if($card_cvv){
            $updates['card_cvv=?'] = $card_cvv;
        }
        if($profile_verify){
            $updates['profile_verify=?'] = $profile_verify;
        }
        $updatesKeys = array_keys($updates);
        $updatesValues = array_values($updates);
        $updatesValues[] = intval($userId);
        if(count($updates)){
            return call_user_func_array([ db(), 'query' ], array_merge([ "UPDATE user SET ".implode(", ", $updatesKeys). " WHERE user_id=?" ], $updatesValues));
        }
        return false;
    }

    public static function updateAvatar($user_id, $avatar) {
        global $db;

        return $db->query("UPDATE `user` SET `avatar`='$avatar' WHERE `user_id`='$user_id'");
    }
}