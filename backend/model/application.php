<?php
class Application {
    public static function create($from, $to, $date, $transport_type, $user_fullname, $user_telephone, $user_email, $user_id, $loading_method, $size, $cargo_size, $photo, $mass, $price, $method, $comment, $quantity) {
        global $db;
        
        $user_id = !empty($user_id) ? "'$user_id'" : "NULL";
        $loading_method = !empty($loading_method) ? "'$loading_method'" : "'0'";
        $cargo_size = !empty($cargo_size) ? "'$cargo_size'" : "''";
        $size = !empty($size) ? "'$size'" : "NULL";
        $photo = !empty($photo) ? "'$photo'" : "NULL";
        $mass = !empty($mass) ? "'$mass'" : "NULL";
        $price = !empty($price) ? "'$price'" : "NULL";
        $comment = !empty($comment) ? "'$comment'" : "NULL";
        $quantity = intval($quantity);
        $transport_type = db("SELECT transport_type_id FROM transport_type WHERE name=?", $transport_type)->fetch_assoc();
        $transport_type = is_array($transport_type) ? $transport_type['transport_type_id'] : 0;
        $result = $db->query("
            INSERT INTO `application`(`from`, `to`, `date`, `transport_type`, `user_fullname`, `user_telephone`, `user_email`, `user_id`, `loading_method`, `size`,`cargo_size`, `photo`, `mass`, `price`,`comment`,`method`, `quantity`)
            VALUES 
            ('$from','$to','$date','$transport_type','$user_fullname','$user_telephone','$user_email',$user_id,$loading_method ,$size, $cargo_size,$photo,$mass,$price,$comment,$method, $quantity)
        ");
        if($result){
            $applicationId = $db->insert_id;
            $application = self::get($applicationId);
            if($method == 1){
                db()->query("UPDATE application SET status=10 WHERE application_id=?", $applicationId);
            }
            $application['size_name'] = str_replace(' ', '', $application['size_name']);
            $application['transport_type_name'] = mb_strtoupper($application['transport_type_name']);
            $message = Email::mail('Application #' . $applicationId, [
                'Application id' => $application['application_id'],
                'Full name' => $application['user_fullname'],
                'Email' => $application['user_email'],
                'Phone' => $application['user_telephone'],
                'From' => $application['from'],
                'To' => $application['to'],
                'Date of download' => $application['date'],
                'Vehicle size' => $application['transport_type_name'],
                'Cargo size' => $application['size_name'] . ' (Quantity: ' . $application['quantity'] . ')',
                'Total weight of cargo' => $application['mass'] . ' lbs',
                'Cargo photo' => $application['photo'] ? '<a href="' . UPLOAD_PATH_LINK . $application['photo'] . '">Show</a>': '-',
                'Driver assistance in loading' => $application['loading_method'] ? 'Yes' : 'No',
                'Price' => $application['price_value'],
                'Comment' => $application['comment'] ? $application['comment'] : '-'
            ], false, [ 'html' => true ]);

            if(TELEGRAM_REPLY_ID && TELEGRAM_BOT_API_KEY){
                $tg = new TelegramBotApi(TELEGRAM_BOT_API_KEY);
                $tg->send(TELEGRAM_REPLY_ID, str_ireplace('<br>', "\n", $message['message']), $application['photo'] ? UPLOAD_PATH . '/' . $application['photo'] : false);
            }
            return $result;
        }
        return false;
    }
    public static function get($where_params = null){
        if(is_int($where_params)){
            $row = db()->query('SELECT a.*,tt.name as transport_type_name, sz.name as size_name, u.avatar as avatar, u.is_online as online, st.name as status_name FROM application as a LEFT JOIN transport_type as tt ON a.transport_type = tt.transport_type_id LEFT JOIN size as sz ON a.size = sz.size_id LEFT JOIN user as u ON u.user_id = a.user_id LEFT JOIN status as st ON st.status_id = a.status WHERE application_id=?', $where_params)->fetch_assoc();
            if($row){
                if($row['size'] == 4){
                    $row['size_name'] = $row['cargo_size'];
                }
                $row['price_value'] = NormalizeView::checkPrice($row['price'], $row['method']);
            }
            return $row;
        }
        global $db;
        return $db->query("SELECT * FROM `application` $where_params");
    }
    public static function edit($from, $to, $date, $transport_type, $loading_method, $size, $cargo_size, $photo, $mass, $price, $method, $comment, $quantity, $application_id) {
        $loading_method = $loading_method ? 1 : 0;
        $size = intval($size);
        $updates = [];
        if($from !== ''){
            $updates['`from`=?'] = $from;
        }
        if($to !== ''){
            $updates['`to`=?'] = $to;
        }
        if($transport_type !== ''){
            $updates['transport_type=?'] = $transport_type;
        }
        if($loading_method !== ''){
            $updates['loading_method=?'] = $loading_method;
        }
        if($size){
            $updates['size=?'] = $size;
        }
        if($cargo_size !== ''){
            $updates['cargo_size=?'] = $cargo_size;
        }
        if($photo !== ''){
            $updates['photo=?'] = $photo;
        }
        if($mass !== ''){
            $updates['mass=?'] = $mass;
        }
        if($price !== ''){
            $updates['price=?'] = $price;
        }
        if($method !== ''){
            $updates['method=?'] = $method;
            if($method == 1){
                $updates['status=?'] = 10;
            }
        }
        if($comment){
            $updates['comment=?'] = $comment;
        }
        if($quantity !== ''){
            $updates['quantity=?'] = $quantity;
        }
        $updates['date_edited=?'] = 'CURRENT_TIMESTAMP';
        $updatesKeys = array_keys($updates);
        $updatesValues = array_values($updates);
        $updatesValues[] = intval($application_id);
        if(count($updates)){
            return call_user_func_array([ db(), 'query' ], array_merge([ "UPDATE application SET ".implode(", ", $updatesKeys). " WHERE application_id=?" ], $updatesValues));
        }
        return false;
    }

    public static function delete($application_id, $user_id) {
        global $db;

        $user_transport_type_id = getDbDate('user', 'user_id', $user_id)->fetch_assoc()['user_type_id'];
        $user_power = getDbDate('user_type', 'user_type_id', $user_transport_type_id)->fetch_assoc()['power'];

        if ($user_power >= 10) {
            return $db->query("DELETE FROM `application` WHERE `application_id` = '$application_id'");
        }

        return $db->query("DELETE FROM `application` WHERE `application_id` = '$application_id' AND `user_id` = '$user_id'");
    }
}