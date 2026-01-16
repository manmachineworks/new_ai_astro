<?php

return [
    'merchant_id' => env('PHONEPE_MERCHANT_ID'),
    'salt_key' => env('PHONEPE_SALT_KEY'),
    'salt_index' => env('PHONEPE_SALT_INDEX'),
    'base_url' => env('PHONEPE_BASE_URL', 'https://api.phonepe.com/apis/hermes'),
    'callback_url' => env('PHONEPE_CALLBACK_URL'),
    'redirect_url' => env('PHONEPE_REDIRECT_URL'),
    'timeout' => (int) env('PHONEPE_TIMEOUT', 15),
];
