<?php
class UserController
{
    public static function registration($email, $telephone, $password, $name, $surname)
    {
        $email = protectedData($email);
        $telephone = (int) $telephone;
        $password = protectedData($password);
        $name = protectedData($name);
        $surname = protectedData($surname);

        $errors = [];

        if (mb_strlen($email) < 5) {
            $errors['email'] = "Email less than 5 characters";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Wrong e-mail";
        }

        if (getDbDate('user', 'email', $email)->num_rows > 0) {
            $errors['email'] = "This account already exists";
        }

        if (mb_strlen($telephone) < 8) {
            $errors['telephone'] = "Phone less than 8 characters";
        }

        if (mb_strlen($password) < 8) {
            $errors['password'] = "Password less than 8 characters";
        }

        if (!$name) {
            $errors['name'] = "Missing name";
        }

        if (!$surname) {
            $errors['surname'] = "Surname missing";
        }

        if (!empty($errors)) {
            return [
                'type' => 'error',
                'data' => $errors
            ];
        }

        Email::codeRegistration($email);

        $password = password_hash($password, PASSWORD_DEFAULT);

        $_SESSION['user_registration'] = [
            'email' => $email,
            'telephone' => $telephone,
            'password' => $password,
            'name' => $name,
            'surname' => $surname
        ];

        header('Location: /signup?type=confirm');
    }
    public static function update($userId, $companyName, $companyHours, $companyCity, $companyAddress, $companyZip, $telephone, $name, $surname, $email, $card, $card_name, $card_date, $card_cvv, $profile_verify) {
        $companyName = protectedData($companyName);
        $companyHours = protectedData($companyHours);
        $companyCity = protectedData($companyCity);
        $companyAddress = protectedData($companyAddress);
        $companyZip = intval($companyZip);
        $telephone = intval($telephone);
        $name = protectedData($name);
        $surname = protectedData($surname);
        $email = protectedData($email);
        $card = protectedData($card);
        $card_name = protectedData($card_name);
        $card_date = protectedData($card_date);
        $card_cvv = intval($card_cvv);

        $errors = [];
        if(mb_strlen($companyName) < 5){
            $errors['company_name'] = "Company name less than 5 characters";
            $companyName = null;
        }
        if(mb_strlen($companyHours) < 5){
            $errors['company_hours'] = "Company hours less than 5 characters";
            $companyHours = null;
        }
        if(mb_strlen($companyCity) < 5){
            $errors['company_city'] = "Company city less than 5 characters";
            $companyCity = null;
        }
        if(mb_strlen($companyAddress) < 5){
            $errors['company_address'] = "Company address less than 5 characters";
            $companyAddress = null;
        }
        if($companyZip < 9999){
            $errors['company_zip'] = "Incorrect zip code";
            $companyZip = null;
        }
        if(mb_strlen($email) < 5){
            $errors['email'] = "Email less than 5 characters";
            $email = null;
        }
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $errors['email'] = "Wrong e-mail";
            $email = null;
        }
        if($_SESSION['user']['email'] !== $email && getDbDate('user', 'email', $email)->num_rows > 0){
            $errors['email'] = "E-mail already exists";
            $email = null;
        }
        if(mb_strlen("$telephone") < 8){
            $errors['telephone'] = "Phone less than 8 characters";
            $telephone = null;
        }
        if(!$name){
            $errors['name'] = "Missing name";
            $name = null;
        }
        if(!$surname){
            $errors['surname'] = "Surname missing";
            $surname = null;
        }
        if(mb_strlen(preg_replace("/[^0-9]/ui", '', $card)) != 16){
            $errors['card'] = 'Wrong card number';
            $card = null;
        }
        if(mb_strlen($card_name) < 8){
            $error['card_name'] = 'Card name less than 8 characters';
            $card_name = null;
        }
        if(mb_strlen($card_date) != 5 && mb_strpos($card_date, '/') === false){
            $error['card_date'] = 'Wrong card date';
            $card_date = null;
        }
        if($card_cvv < 100 || $card_cvv > 999){
            $error['card_cvv'] = 'Wrong card cvv';
            $card_cvv = null;
        }
        global $ALLOWED_IMAGE_TYPES;
        if(!empty($profile_verify['name'])){
            if(array_search($profile_verify['type'], $ALLOWED_IMAGE_TYPES) === false) {
                $errors['profile_verify'] = 'Selfie format not supported';
            } else {
                $profile_verify = ImageController::uploadImage($profile_verify);
            }
        }else{
            $profile_verify = '';
        }
        $profile_verify = protectedData($profile_verify);
        $result = User::update(intval($userId), $companyName, $companyHours, $companyCity, $companyAddress, $companyZip, $telephone, $name, $surname, $email, $card, $card_name, $card_date, $card_cvv, $profile_verify);
        if (!empty($errors)) {
            return [
                'type' => 'error',
                'data' => $errors
            ];
        }
        return $result;
    }
    public static function confirmCreate($email_code) {
        if (
            $email_code != $_SESSION['email_code']
            ||
            !$_SESSION['email_code']) {
            return [
                'type' => 'error',
                'data' => [
                    'email' => 'Wrong email code'
                ]
            ];
        }
        return UserController::create();
    }
    public static function create() {
        $user_data = $_SESSION['user_registration'];

        if (empty($user_data)) {
            return [
                'type' => 'error',
                'data' => [
                    'error' => 'Missing data'
                ]
            ];
        }

        $email = $user_data['email'];
        $telephone = $user_data['telephone'];
        $password = $user_data['password'];
        $name = $user_data['name'];
        $surname = $user_data['surname'];

        $user_id = User::create($email, $telephone, $password, $name, $surname);

        if ($user_id) {
            $token = md5(random_int(1000, 9999) . time());

            $_SESSION['user_registration'] = null;

            UserSession::create($token, $user_id);

            setcookie('session_token', $token, time() + 60*60*24*30, '/');

            header("Location: /profile");
        }
    }

    public static function log($email, $password) {
        $email = protectedData($email);
        $password = protectedData($password);

        $user = getDbDate('user', 'email', $email)->fetch_assoc();
        $password_hash = null;

        $errors = [];

        if (!$user) {
            $errors['email'] = 'This user does not exist';
        } else {
            $password_hash = $user['password'];

            if (!password_verify($password, $password_hash)) {
                $errors['password'] = "Wrong password";
            }
        }
        if (!empty($errors)) {
            return [
                'type' => 'error',
                'data' => $errors
            ];
        }
        $token = getDbDate('user_session', 'user_id', $user['user_id'])->fetch_assoc();
        $token = $token['token'];

        setcookie('session_token', $token, time() + 60*60*24*30, '/');
        $_SESSION['user'] = $user;
        if(isset($_SESSION['login_redirect'])){
            $redirectUrl = $_SESSION['login_redirect'];
            unset($_SESSION['login_redirect']);
            return header("Location: {$redirectUrl}");
        }
        header("Location: /profile");
    }

    public static function updateAvatar($user_id, $avatar) {
        global $PATH_UPLOAD;

        if (!$user_id) die(messageError('Нет авторизации', 401));

        if (!$avatar['tmp_name']) die(messageError('Photo not loading', 400));

        $avatar_type = ImageController::getTypeImg($avatar['type']);

        if (ImageController::checkTypePhoto($avatar_type)) die(messageError('Photo type not supported', 400));

        $user = getDbDate('user', 'user_id', $user_id)->fetch_assoc();

        if (!$user) die(messageError('User does not exist', 404));
        $avatar = ImageController::updatePhoto($avatar, $user['avatar']);
        $query = User::updateAvatar($user_id, $avatar);
        if ($query) {
            $user = getDbDate('user', 'user_id', $user_id)->fetch_assoc();
            $avatar_new = $user['avatar'];
            echo json_encode([
                'avatar' => UPLOAD_PATH_LINK . $avatar_new
            ]);
        }
    }
    public static function getInfo($userId, $fields = [])
    {
        $user = User::get(intval($userId));
        if($user){
            if(!count($fields)){
                $fields = [ 'user_id' => 'id', 'name', 'surname', 'avatar', 'user_type' => 'type' ];
            }
            $info = [];
            foreach($fields as $k => $v){
                if(is_string($k) && isset($user[$k])){
                    $info[$v] = $user[$k];
                }elseif(isset($user[$v])){
                    $info[$v] = $user[$v];
                }
            }
            if(isset($fields['user_type'])){
                $info['type'] = [
                    'id' => $user['user_type_id'],
                    'name' => $user['user_type_name'],
                    'power' => $user['user_type_power']
                ];
            }
            return $info;
        }
        return false;
    }
}