<?php

function messageError($message, $code) {
    http_response_code($code);

    return json_encode([
        'type' => 'error',
        'message' => $message
    ]);
}

function apiError($message)
{
    return [
        'error' => $message
    ];
}
function apiResult($result)
{
    if(!is_array($result) || !isset($result['error'])){
        $result = [ 'response' => $result ];
    }
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    die;
}
function user($showError = true)
{
    if(isset($_SESSION['user'])){
        return $_SESSION['user'];
    }
    if($showError){
        apiResult(apiError('User no auth'));
    }
}