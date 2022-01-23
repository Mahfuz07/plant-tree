<?php

if (!defined('PROJECT_PATH')) {
    define('PROJECT_PATH', 'http://localhost:8765');
}

if (!defined('API_DOMAIN_URL')) {
    define('API_DOMAIN_URL', 'https://sandbox.sslcommerz.com'); // For Sandbox
    //define('API_DOMAIN_URL', 'https://securepay.sslcommerz.com'); // For Live
}

if (!defined('STORE_ID')) {
    define('STORE_ID', 'abc61e97ed13485d');
}

if (!defined('STORE_PASSWORD')) {
    define('STORE_PASSWORD', 'abc61e97ed13485d@ssl');
}

if (!defined('IS_LOCALHOST')) {
    define('IS_LOCALHOST', true);
}

return [
    'projectPath' => constant("PROJECT_PATH"),
    'apiDomain' => constant("API_DOMAIN_URL"),
    'apiCredentials' => [
        'store_id' => constant("STORE_ID"),
        'store_password' => constant("STORE_PASSWORD"),
    ],
    'apiUrl' => [
        'make_payment' => "/gwprocess/v4/api.php",
        'transaction_status' => "/validator/api/merchantTransIDvalidationAPI.php",
        'order_validate' => "/validator/api/validationserverAPI.php",
        'refund_payment' => "/validator/api/merchantTransIDvalidationAPI.php",
        'refund_status' => "/validator/api/merchantTransIDvalidationAPI.php",
    ],
    'connect_from_localhost' => constant("IS_LOCALHOST"),
    'success_url' => 'api/payment/success',
    'failed_url' => 'api/payment/fail',
    'cancel_url' => 'api/payment/cancel',
];
