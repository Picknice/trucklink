<?php
class ApplicationController {
    public static function firstCreate($from, $to, $date, $transport_type) {
        $from = protectedData($from);
        $to = protectedData($to);
        $date = DateView::normalizeDateSql($date);
        $transport_type = protectedData($transport_type);

        $errors = [];

        if (mb_strlen($from) < 2) {
            $errors['from'] = "Where is not specified";
        }

        if (mb_strlen($to) < 2) {
            $errors['to'] = "Not specified where";
        }

        if (!$date) {
            $errors['date'] = "no date";
        }

        if (!$transport_type) {
            $errors['transport_type'] = "No transport selected";
        }

        if (!empty($errors)) {
            return [
                'type' => 'error',
                'data' => $errors
            ];
        }

        $_SESSION['application'] = [
            'from' => $from,
            'to' => $to,
            'date' => $date,
            'transport_type' => $transport_type
        ];
        header('Location: ./create');
    }

    public static function secondCreate($fullname, $telephone, $email) {
        $fullname = protectedData($fullname);
        $telephone = normalizeTepelhone($telephone);
        $email = protectedData($email);

        $errors = [];

        if (mb_strlen($fullname) < 3) {
            $errors['fullname'] = "Less than 3 characters";
        }

        if (mb_strlen($telephone) < 7) {
            $errors['telephone'] = "Less than 7 characters";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Wrong mail";
        }

        if (!empty($errors)) {
            return [
                'type' => 'error',
                'data' => $errors
            ];
        }

        $_SESSION['application']['fullname'] = $fullname;
        $_SESSION['application']['telephone'] = $telephone;
        $_SESSION['application']['email'] = $email;
        header('Location: ./create?page=2');
    }

    public static function thirdCreate($loading_method, $size, $cargo_size, $photo, $mass, $price, $comment, $method, $quantity) {
        global $ALLOWED_IMAGE_TYPES;
        $errors = [];

        if($loading_method === null){
            $errors['loading_method'] = 'No select driver assistance in loading';
        }
        if($size === null){
            $errors['size'] = 'No select cargo size';
        }else if($size == 4 && ($cargo_size == '' || count(explode('X', $cargo_size)) !== 3)){
            $errors['cargo_size'] = 'Cargo size not specified';
        }
        $mass = floatval($mass);
        if(!$mass){
            $errors['mass'] = 'Weight not specified';
        }
        $price = floatval($price);
        if($method === null){
            $errors['method'] = 'No select payment method';
        }else if( $method == 3 && !$price){
            $errors['price'] = 'Price not specified';
        }
        if (!empty($photo['name'])){
            if(array_search($photo['type'], $ALLOWED_IMAGE_TYPES) === false) {
                $errors['photo'] = 'Photo format not supported';
            } else {
                $photo = ImageController::uploadImage($photo);
            }
        }else{
            $photo = null;
        }
        $quantity = intval($quantity);
        if($quantity < 1){
            $errors['quantity'] = 'Quantity not specified';
        }
        if (!empty($errors)) {
            return [
                'type' => 'error',
                'data' => $errors
            ];
        }
        $loading_method = intval($loading_method);
        $size = intval($size);
        $method = intval($method);

        $comment = protectedData($comment);

        $_SESSION['application']['loading_method'] = $loading_method;
        $_SESSION['application']['size'] = $size;
        $_SESSION['application']['cargo_size'] = $cargo_size;
        $_SESSION['application']['mass'] = $mass;
        $_SESSION['application']['photo'] = $photo;
        $_SESSION['application']['method'] = $method;
        $_SESSION['application']['comment'] = $comment;


        $from = $_SESSION['application']['from'];
        $to = $_SESSION['application']['to'];
        $date = $_SESSION['application']['date'];
        $transport_type = $_SESSION['application']['transport_type'];
        $fullname = $_SESSION['application']['fullname'];
        $user_id = $_SESSION['user']['user_id'];
        $telephone = $_SESSION['application']['telephone'];
        $email = $_SESSION['application']['email'];

        $query = Application::create($from, $to, $date, $transport_type, $fullname, $telephone, $email, $user_id, $loading_method, $size, $cargo_size, $photo, $mass, $price, $method, $comment, $quantity);
        if ($query) {
            header('Location: ./create?page=3');
        }
    }

    public static function edit($from, $to, $date, $transport_type, $loading_method, $size, $cargo_size, $photo, $mass, $price, $method, $comment, $quantity, $application_id) {
        global $ALLOWED_IMAGE_TYPES;
        $errors = [];
        $from = protecteddata($from);
        $to = protecteddata($to);
        $date = dateview::normalizedatesql($date);

        if($transport_type === null){
            $errors['transport_type'] = 'no select transport tipe';
        }
        if($loading_method === null){
            $errors['loading_method'] = 'no select driver assistance in loading';
        }
        if($size === null){
            $errors['size'] = 'no select cargo size';
        }else if($size == 4 && ($cargo_size == '' || count(explode('X', $cargo_size)) !== 3)){
            $errors['cargo_size'] = 'cargo size not specified';
        }
        $mass = floatval($mass);
        if(!$mass){
            $errors['mass'] = 'weight not specified';
        }
        $price = floatval($price);
        if($method === null){
            $errors['method'] = 'no select payment method';
        }else if( $method == 3 && !$price){
            $errors['price'] = 'price not specified';
        }
        if($quantity < 1){
            $errors['quantity'] = 'Quantity not specified';
        }
        if(is_array($photo)) {
            if (!empty($photo['name'])){
                if(array_search($photo['type'], $ALLOWED_IMAGE_TYPES) === false) {
                    $errors['photo'] = 'Photo format not supported';
                } else {
                    $photo = ImageController::uploadImage($photo);
                }
            }else{
                $photo = '';
            }
        }
        if (!empty($errors)) {
            return [
                'type' => 'error',
                'data' => $errors
            ];
        }
        $transport_type = intval($transport_type);
        $loading_method = intval($loading_method);
        $size = intval($size);
        $method = intval($method);

        $comment = protecteddata($comment);
        $query = application::edit($from, $to, $date, $transport_type, $loading_method, $size, $cargo_size, $photo, $mass, $price, $method, $comment, $quantity, $application_id);
        if ($query) {
            header("refresh:0");
        }
    }

    public static function httpDelete($application_id, $user_id) {
        $error = '';
        $code = 200;

        if (!$application_id) {
            $code = 404;

            $error = 'application not found';
        }

        if (!$user_id) {
            $code = 401;

            $error = 'no authorization';
        }

        if ($error) {
            http_response_code($code);

            echo json_encode(
                [
                    'type' => 'error',
                    'message' => $error
                ]
            );

            return;
        }

        $query = Application::delete($application_id, $user_id);

        if ($query) {
            echo json_encode(
                [
                    'message' => 'application deleted'
                ]
            );
            return;
        }

        http_response_code(403);

        echo json_encode(
            [
                'type' => 'error',
                'message' => 'no access'
            ]
        );
    }
}
