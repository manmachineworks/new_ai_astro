<?php

return [
    'merchant_id' => env('PHONEPE_MERCHANT_ID'),
    'salt_key' => env('PHONEPE_SALT_KEY'),
    'salt_index' => env('PHONEPE_SALT_INDEX', 1),
    'env' => env('PHONEPE_ENV', 'sandbox'),
    'base_url' => env('PHONEPE_BASE_URL', env('PHONEPE_ENV', 'sandbox') === 'production'
        ? 'https://api.phonepe.com/apis/hermes'
        : 'https://api-preprod.phonepe.com/apis/pg-sandbox'),
    'webhook_secret' => env('PHONEPE_WEBHOOK_SECRET'),
    'redirect_url' => env('APP_URL') . '/wallet/recharge/return',
    'callback_url' => env('APP_URL') . '/webhooks/phonepe',
];
