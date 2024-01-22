<?php
class Email {
    public static function mail($subject, $message, $to = false, $options = [])
    {
        $isHtml = isset($options['html']) && $options['html'];
        $clearHtml = '';
        if(is_array($message)){
            $tmp = [];
            $assoc = false;
            foreach($message as $k => $v){
                if(!$assoc && is_string($k)){
                    $assoc = true;
                }
                $tmp[] = $isHtml ? $k . ': <b>' . $v . '</b>' : $k . ': ' . $v;
            }
            $message = implode($isHtml ? "<br>" : "\r\n", $assoc ? $tmp : $message);
            if($isHtml) {
                $clearHtml = $message;
                $message = '<html><body>' . $message . '</body></html>';
            }
        }
        if($to === false){
            $to = EMAIL_REPLY_APPLICATION;
        }
        $result = sendmail($to, $subject, $message, $options);
        return [
            'message' => $clearHtml,
            'result' => $result
        ];
    }
    public static function codeRegistration($email)
    {
        try {
            $_SESSION['email_code'] = random_int(100000, 999999);
        } catch (Exception $e) {
        }

        error_log("MAIL: $email", 0);

        $subject = 'Registration';
        $message = "Your registration confirmation code: {$_SESSION['email_code']}";
        $result = self::mail($subject, $message, $email);
        
        error_log("RESULT: $result", 0);
        
        if (!$result) {
            return [
                'type' => 'error',
                'data' => [
                    'email' => 'Failed to send code'
                ]
            ];
        }
    }
}