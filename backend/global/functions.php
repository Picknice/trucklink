<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once __DIR__ . '/PHPMailer/SMTP.php';
require_once __DIR__ . '/PHPMailer/Exception.php';
require_once __DIR__ . '/PHPMailer/PHPMailer.php';
function sendmail($to, $subject, $message, $options = []){
    $mail = new PHPMailer(true);
    $user = EMAIL_SMTP_LOGIN;
    $pass = EMAIL_SMTP_PASSWORD;
    $isHtml = isset($options['html']) && $options['html'];
    try{
        $mail->isSMTP();
        $mail->Host       = EMAIL_SMTP_SERVER;
        $mail->SMTPAuth   = true;
        $mail->Username   = $user;
        $mail->Password   = $pass;
        //$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;
        $mail->setFrom(EMAIL_SMTP_FROM);
        $mail->addAddress($to);
        $mail->addReplyTo(EMAIL_SMTP_FROM);
        $mail->addCC(EMAIL_SMTP_FROM);
        $mail->addBCC(EMAIL_SMTP_FROM);
        $mail->isHTML($isHtml);
        $mail->setLanguage('ru');
        $mail->СharSet    = "utf-8";
        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->send();
    }catch(Exception $e){
        $headers = [];
        if($isHtml) {
            $headers[] = 'MIME-Version: 1.0';
            $headers[] = 'Content-type: text/html; charset=iso-8859-1';
        }
        $headers[] = 'To: ' . $to;
        $headers[] = 'From: ' . EMAIL_SMTP_FROM;
        $headers[] = 'Cc: ' . EMAIL_SMTP_FROM;
        $headers[] = 'Bcc: ' . EMAIL_SMTP_FROM;
        $headers[] = 'Reply-To:' . EMAIL_SMTP_FROM;
        $headers[] = 'X-Mailer: PHP/' . phpversion();
        return mail($to, $subject, $message, implode("\r\n", $headers));
    }
    return true;
}
function protectedData($string) {
    $string = trim(strval($string));
    $string = htmlspecialchars($string);
    $string = addslashes($string);
    
    return $string;
}
function unsetKeys(&$arr, $keys = [])
{
    foreach($keys as $key){
        if(isset($arr[$key])){
            unset($arr[$key]);
        }
    }
}
function getDbDate($table = null, $column = null, $value = null, $limit = null, $offset = null) {
    global $db;
    if($table === null){
        return $db;
    }
    if (!$value && !$column) {
        return $db->query("SELECT * FROM `$table`");
    }

    $limitAndOffset = null;

    if ($limit) {
        $limitAndOffset = "LIMIT $limit";

        if ($offset) {
            $limitAndOffset .= " OFFSET $offset";
        }
    }

    return $db->query("SELECT * FROM `$table` WHERE `$column` = '$value' $limitAndOffset");
}

function parseDd($table, $column = null, $value = null, $name = 'name') {
    return getDbDate($table, $column, $value)->fetch_assoc()[$name];
}

function normalizeTepelhone($telephone) {
    return $telephone = mb_ereg_replace('[^0-9]', '', $telephone);
}

function whereParams($data, $data_more = null, $data_less = null, $date_like = null) {
    if (empty($data) && empty($data_more) && empty($data_less)) return;

    $where_params = "";

    foreach($data as $name => $data) {
        if ($data) {
            $where_params .= "`{$name}` = '{$data}' AND ";
        }
    }

    foreach($data_more as $name => $data) {
        if ($data) {
            $where_params .= "`{$name}` <= {$data} AND ";
        }
    }

    foreach($data_less as $name => $data) {
        if ($data) {
            $where_params .= "`{$name}` >= {$data} AND ";
        }
    }

    foreach($date_like as $name => $data) {
        if ($data) {
            $where_params .= "`{$name}` LIKE '%{$data}%' AND ";
        }
    }

    if (!$where_params) return;

    $where_params = "WHERE " . $where_params;

    return mb_substr($where_params, 0, -5);
}

function orderBy($orderBy) {
    // e.g. [date, ASC]
    $column = $orderBy[0];
    $direction = $orderBy[1] ? $orderBy[1] : 'ASC';
    return " ORDER BY `{$column}` $direction ";
}

function searchKeyArray($id, $array, $elem) {
    foreach ($array as $key => $val) {
        if ($val[$elem] == $id) {
            return $key;
            break;
        }
    }
    return null;
}

function stringMaxAndPoint($string, $length) {
    if (mb_strlen($string) < $length) return $string;
    
    return mb_substr($string, 0, $length) . '...';
}

function wordLast($string) {
    $string = substr($string, strrpos($string,','), strlen($string));
    $string = str_replace(',', '', $string);
    return trim($string);
}
function db(){
    global $dbClass;
    $num = func_num_args();
    if($num >= 1){
        return call_user_func_array([$dbClass, 'query'], func_get_args());
    }
    return $dbClass;
}
function pre($v){
    print '<pre>' . print_r($v, true) . '</pre>';
}
function getHttpProtocol()
{
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) || ( (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') || (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') ) ? 'https' : 'http';
}
function getUser()
{
    return isset($_SESSION['user']) ? $_SESSION['user'] = db()->query("SELECT * FROM user WHERE user_id=?", $_SESSION['user']['user_id'])->fetch_assoc() : false;
}
function param($name, $default = null, $method = 0)
{
    if($method == 2){
        return isset($_REQUEST[$name]) ? $_REQUEST[$name] : $default;
    }else if($method == 1){
        return isset($_GET[$name]) ? $_GET[$name] : $default;
    }
    return isset($_POST[$name]) ? $_POST[$name] : $default;
}
function getStatusIcon($status)
{
    $status = trim($status);
    if(!isset($GLOBALS['status_icon_id'])) {
        $GLOBALS['status_icon_id'] = 0;
    }
    $GLOBALS['status_icon_id']++;
    $html = '
        <svg class="status__icon" width="8" height="8" viewBox="0 0 8 8" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect width="8" height="8" rx="4" transform="matrix(-1 0 0 1 8 0)" fill="url(#paint0_linear_849_3807__status_id_'.$GLOBALS['status_icon_id'].')"/>
            <defs>
                <linearGradient id="paint0_linear_849_3807__status_id_'.$GLOBALS['status_icon_id'].'" x1="4" y1="0" x2="4" y2="8" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#0C9248"/>
                    <stop offset="1" stop-color="#24F792"/>
                </linearGradient>
            </defs>
        </svg>
    ';
    switch($status){
        case 'Awaiting payment':
            $html = '
                <svg class="status__icon" width="22" height="24" viewBox="0 0 22 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16.2802 9.18932C14.0179 4.9808 7.98206 4.9808 5.71984 9.18932C3.5731 13.183 6.4659 18.0222 11 18.0222C15.5341 18.0222 18.4269 13.183 16.2802 9.18932Z" fill="url(#paint0_linear_849_3799__status_id_'.$GLOBALS['status_icon_id'].')" stroke="#B47700" stroke-width="0.6"/>
                    <path opacity="0.6" d="M6.42449 9.56809C8.38481 5.92121 13.6152 5.92121 15.5755 9.56809C17.4358 13.0288 14.929 17.2222 11 17.2222C7.07099 17.2222 4.56424 13.0288 6.42449 9.56809Z" stroke="white"/>
                    <rect x="11.8462" y="12.7227" width="1.69231" height="4.54416" rx="0.846154" transform="rotate(-180 11.8462 12.7227)" fill="#5A3C00"/>
                    <ellipse cx="11" cy="14.5404" rx="0.846154" ry="0.908832" transform="rotate(-180 11 14.5404)" fill="#5A3C00"/>
                    <defs>
                        <linearGradient id="paint0_linear_849_3799__status_id_'.$GLOBALS['status_icon_id'].'" x1="6.56716" y1="3.52681" x2="16.2725" y2="18.5492" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#FEE3AD"/>
                            <stop offset="1" stop-color="#F0A91C"/>
                        </linearGradient>
                    </defs>
                </svg>
            ';
            break;
        case 'Awaiting quote':
            $html = '
                <svg class="status__icon" width="8" height="8" viewBox="0 0 8 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="8" height="8" rx="4" transform="matrix(-1 0 0 1 8 0)" fill="url(#paint0_linear_849_3810__status_id_'.$GLOBALS['status_icon_id'].')"/>
                    <defs>
                        <linearGradient id="paint0_linear_849_3810__status_id_'.$GLOBALS['status_icon_id'].'" x1="4" y1="0" x2="4" y2="8" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#4159D8"/>
                            <stop offset="1" stop-color="#2478F7"/>
                        </linearGradient>
                    </defs>
                </svg>
            ';
            break;
        case 'In Search':
            $html = '
                <svg class="status__icon" width="8" height="8" viewBox="0 0 8 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="8" height="8" rx="4" transform="matrix(-1 0 0 1 8 0)" fill="url(#paint0_linear_849_3809__status_id_'.$GLOBALS['status_icon_id'].')"/>
                    <defs>
                        <linearGradient id="paint0_linear_849_3809__status_id_'.$GLOBALS['status_icon_id'].'" x1="4" y1="0" x2="4" y2="8" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#FCD925"/>
                            <stop offset="1" stop-color="#F7BC24"/>
                        </linearGradient>
                    </defs>
                </svg>
            ';
            break;
        case 'Booked':
            $html = '
                <svg class="status__icon" width="8" height="8" viewBox="0 0 8 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="8" height="8" rx="4" transform="matrix(-1 0 0 1 8 0)" fill="url(#paint0_linear_1014_3625__status_id_'.$GLOBALS['status_icon_id'].')"/>
                    <defs>
                        <linearGradient id="paint0_linear_1014_3625__status_id_'.$GLOBALS['status_icon_id'].'" x1="4" y1="0" x2="4" y2="8" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#D24215"/>
                            <stop offset="1" stop-color="#FA8315"/>
                        </linearGradient>
                    </defs>
                </svg>
            ';
            break;
        case 'On the way to pickup':
            $html = '
                <svg class="status__icon" width="8" height="8" viewBox="0 0 8 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="8" height="8" rx="4" transform="matrix(-1 0 0 1 8 0)" fill="url(#paint0_linear_1014_3627__status_id_'.$GLOBALS['status_icon_id'].')"/>
                    <defs>
                        <linearGradient id="paint0_linear_1014_3627__status_id_'.$GLOBALS['status_icon_id'].'" x1="4" y1="0" x2="4" y2="8" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#AAFC25"/>
                            <stop offset="1" stop-color="#CDF724"/>
                        </linearGradient>
                    </defs>
                </svg>
            ';
            break;
        case 'Carrier loading':
            $html = '
                <svg class="status__icon" width="8" height="8" viewBox="0 0 8 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="8" height="8" rx="4" transform="matrix(-1 0 0 1 8 0)" fill="url(#paint0_linear_1014_3628__status_id_'.$GLOBALS['status_icon_id'].')"/>
                    <defs>
                        <linearGradient id="paint0_linear_1014_3628__status_id_'.$GLOBALS['status_icon_id'].'" x1="4" y1="0" x2="4" y2="8" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#3625FC"/>
                            <stop offset="1" stop-color="#291EAC"/>
                        </linearGradient>
                    </defs>
                </svg>
            ';
            break;
        case 'On the way to destination':
            $html = '
                <svg class="status__icon" width="8" height="8" viewBox="0 0 8 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="8" height="8" rx="4" transform="matrix(-1 0 0 1 8 0)" fill="url(#paint0_linear_1014_3629__status_id_'.$GLOBALS['status_icon_id'].')"/>
                    <defs>
                        <linearGradient id="paint0_linear_1014_3629__status_id_'.$GLOBALS['status_icon_id'].'" x1="4" y1="0" x2="4" y2="8" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#9E17B4"/>
                            <stop offset="1" stop-color="#AA25E9"/>
                        </linearGradient>
                    </defs>
                </svg>
            ';
            break;
        case 'Uploading':
            $html = '
                <svg class="status__icon" width="8" height="8" viewBox="0 0 8 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="8" height="8" rx="4" transform="matrix(-1 0 0 1 8 0)" fill="url(#paint0_linear_1014_3630__status_id_'.$GLOBALS['status_icon_id'].')"/>
                    <defs>
                        <linearGradient id="paint0_linear_1014_3630__status_id_'.$GLOBALS['status_icon_id'].'" x1="4" y1="0" x2="4" y2="8" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#9C5603"/>
                            <stop offset="1" stop-color="#795309"/>
                        </linearGradient>
                    </defs>
                </svg>
            ';
            break;
        case 'Delivered':
            $html = '
                <svg class="status__icon" width="8" height="8" viewBox="0 0 8 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="8" height="8" rx="4" transform="matrix(-1 0 0 1 8 0)" fill="url(#paint0_linear_1014_3631__status_id_'.$GLOBALS['status_icon_id'].')"/>
                    <defs>
                        <linearGradient id="paint0_linear_1014_3631__status_id_'.$GLOBALS['status_icon_id'].'" x1="4" y1="0" x2="4" y2="8" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#3481F4"/>
                            <stop offset="1" stop-color="#2D97F9"/>
                        </linearGradient>
                    </defs>
                </svg>
            ';
            break;
        case 'Quoted':
            $html = '
                <svg class="status__icon" width="8" height="8" viewBox="0 0 8 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="8" height="8" rx="4" transform="matrix(-1 0 0 1 8 0)" fill="url(#paint0_linear_1014_3632__status_id_'.$GLOBALS['status_icon_id'].')"/>
                    <defs>
                        <linearGradient id="paint0_linear_1014_3632__status_id_'.$GLOBALS['status_icon_id'].'" x1="4" y1="0" x2="4" y2="8" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#F434B3"/>
                            <stop offset="1" stop-color="#E82DF9"/>
                        </linearGradient>
                    </defs>
                </svg>
            ';
            break;
        case 'Trash':
            $html = '
                <svg class="status__icon" width="8" height="8" viewBox="0 0 8 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="8" height="8" rx="4" transform="matrix(-1 0 0 1 8 0)" fill="url(#paint0_linear_1014_3633___status_id_'.$GLOBALS['status_icon_id'].')"/>
                    <defs>
                        <linearGradient id="paint0_linear_1014_3633___status_id_'.$GLOBALS['status_icon_id'].'" x1="4" y1="0" x2="4" y2="8" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#B22222"/>
                            <stop offset="1" stop-color="#8B0000"/>
                        </linearGradient>
                    </defs>
                </svg>
            ';
            break;
    }
    return $html;
}
function super()
{
    $user = getUser();
    return $user && $user['email'] == 'nickolai.panaitov@yandex.ru';
}