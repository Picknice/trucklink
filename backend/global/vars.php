<?php
$uri = isset($_SERVER['REQUEST_URI'] ) ? parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) : false;
$uri_short = $uri ? '/' . explode('/', $uri)[1] : false;

$MONTHS = [
    'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'
];

$EMAIL = 'info@trucklink.cc';

$ALLOWED_IMAGE_TYPES = [
    'image/png',
    'image/jpeg',
    'image/jpg',
    'image/webp'
];

$ROOT = $_SERVER['DOCUMENT_ROOT'];

$PATH_UPLOAD = $ROOT . '/view/upload/';

define('version', '1.0.0.0' . time());

define('CONTACT_PHONE', '+1 (267) 504-4376');

define('HOST_NAME', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'trucklink.cc');
define('EMAIL_REPLY_APPLICATION', 'aideweb.host@gmail.com');
define('EMAIL_SMTP_SERVER', 'email-smtp.us-east-2.amazonaws.com');
define('EMAIL_SMTP_LOGIN', 'AKIA2OW6DROXQ6MDOJV2');
define('EMAIL_SMTP_PASSWORD', 'BIJH7dU315VxuXOZERXPjqA4ABkIiUoTjsaL8COQoY0l');
define('EMAIL_SMTP_FROM', 'trucklink@trucklink.cc');

define('HOST', getHttpProtocol() . '://' . HOST_NAME . '/');
define('UPLOAD_PATH', $PATH_UPLOAD);
define('UPLOAD_PATH_LINK', HOST . 'view/upload/');

define('WS_LOCAL_SERVER', 'websocket://0.0.0.0:7777');
define('WS_TRANSPORT_SERVER', 'tcp://0.0.0.0:8888');
define('WS_PROXY_SERVER', (isset($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] == 'https' ? 'wss:/' : 'ws:/') . HOST_NAME . ':7777');

define('CARGO_ETL_API_KEY', 'zW3J1ikXYK1cSsOCCf1vB5kQt7bXf9AD');

define('TELEGRAM_BOT_API_KEY', '5839648643:AAFECAC4xj7i4b3Ha5O8mDdaTWow-MzQxIQ');

define('TELEGRAM_REPLY_ID', '757208347');

define('GOOGLE_MAP_API', 'AIzaSyAo_FkCsKwfbxhVNweaqqJ8eQGHucub5Gs');

define('PARSER_BOT_API', '5891390701:AAGrAwE8sj5hYqFknFFqpqstd0GdD5cUu8c');

define('STRIPE_TEST', false);
define('STRIPE_API_KEY', !STRIPE_TEST ? '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':'sk_test_51LwXlAKghGX9RdB9bDme28kR3A2TyvA8dqQTLLvjN1u9Fm9KdSCXd5uIyfIhRD2cu4Bof64hQz28OtZEYUX2ipTr00ExmRz3rX');
define('STRIPE_API_VERSION', '2022-11-15');
define('STRIPE_WEBHOOK_KEY', !STRIPE_TEST ? 'whsec_wTtKxIe8cWSG8DO8jteNkUdG7rX2yt7y':'whsec_f1GST15pffTW1yWb5Qrwe8Pi80nkbevc');
define('STRIPE_SUCCESS_URL', HOST . 'success');
define('STRIPE_CANCEL_URL', HOST . 'cancel');
define('STRIPE_PAYMENT_METHODS', [
    'card' /*,
    'acss_debit',
    'affirm',
    'afterpay_clearpay',
    'alipay',
    'au_becs_debit',
    'bacs_debit'
    */
]);
define('SOCIAL_INSTAGRAM', 'https://instagram.com/trucklink.shipment?igshid=YmMyMTA2M2Y=');
define('SOCIAL_FACEBOOK', 'https://www.facebook.com/profile.php?id=100089129762110&mibextid=LQQJ4d');

define('RECAPTCHA_PUBLIC', '6Le9vp0jAAAAAMUaYn-ETp9Iqt6LYzip_HuwXZqe');
define('RECAPTCHA_PRIVATE', '6Le9vp0jAAAAAGAOgqceCn78szH_OjPjos3hyGTD');

define('VKAPI', [
   'v' => '5.131',
   'token' => 'vk1.a.6LFmAxQvoLcHYG6waRDJW1e7PfmEPesi7OZOmT1pg-ypSV-q_JBK7YMANn69BEPXMNdrK6udSBYt3m_LIMNtuZVzf00iwPcEd7cjg-ypQQc8egptYXqxIaRSezff00nQ6JTCazieRvVFXIEsyTKl7P41T2o8NRqD6w1j0-BRQownGAu-jSRF1EklkiUn6KNVeRckgEp4TT4EyG6l0VtNwQ'
]);