<?php
require_once realpath(dirname(__DIR__) . '/../../vendor/autoload.php');
use Cartalyst\Stripe\Stripe;
$stripe = new Stripe(STRIPE_API_KEY, STRIPE_API_VERSION);
$applicationId = intval(param('application_id', 0, 1));
if($applicationId){
    $user = getUser();
    if(!$user) {
        header('Location: /');
    }
    $application = Application::get($applicationId);

    if(!$application){
        header('Location: /');
    }
    if($application['status'] != 1){
        header('Location: /');
    }
    if($application['user_id'] != $user['user_id']){
        header('Location: /');
    }
    $price = intval($application['price']);
    if(!$price){
        header('Location: /');
    }
    if($application['pay_url'] && time() < $application['pay_expires']){
        header('Location: '. $application['pay_url']);
    }else {
        $expires = time() + 24 * 3600;
        $checkout = $stripe->checkout()->sessions()->create([
            'line_items' => [[
                'price_data' => [
                    'currency' => 'USD',
                    'product_data' => [
                        'name' => 'Application #' . $application['application_id'],
                    ],
                    'unit_amount' => $price * 100
                ],
                'quantity' => 1,
            ]],
            'payment_method_types' => STRIPE_PAYMENT_METHODS,
            'expires_at' => $expires,
            'metadata' => [
                'application_id' => $application['application_id']
            ],
            'mode' => 'payment',
            'success_url' => STRIPE_SUCCESS_URL,
            'cancel_url' => STRIPE_CANCEL_URL,
        ]);
        if (!is_array($checkout)) {
            header('Location: /');
        } else {
            db()->query("UPDATE application SET pay_id=?,pay_url=?,pay_created=?,pay_expires=? WHERE application_id=?", $checkout['id'], $checkout['url'], time(), $expires, $application['application_id']);
            header('Location: '. $checkout['url']);
        }
    }
}else{
    $payload = @file_get_contents('php://input');
    $sig_header = isset($_SERVER['HTTP_STRIPE_SIGNATURE']) ? $_SERVER['HTTP_STRIPE_SIGNATURE'] : '';
    parse_str(str_replace(',', '&', $sig_header), $sigData);
    if(is_array($sigData) && count($sigData) == 3){
        $timestamp = intval($sigData['t']);
        $sigPayloadHash = hash_hmac('sha256', "{$timestamp}.{$payload}", STRIPE_WEBHOOK_KEY);
        if(hash_equals($sigData['v1'], $sigPayloadHash)){
            $payload = @json_decode($payload, true);
            $success = $payload['type'] == 'checkout.session.completed';
            $applicationId = intval($payload['data']['object']['metadata']['application_id']);
            if($applicationId){
                $application = Application::get($applicationId);
                db()->query("UPDATE application SET status=?,pay_status=? WHERE application_id=?", $success ? 2 : 12, $success ? 2 : 1, $applicationId);
            }
            http_response_code(200);
        }
    }
}