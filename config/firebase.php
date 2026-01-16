<?php

return [
    'project_id' => env('FIREBASE_PROJECT_ID'),
    'client_email' => env('FIREBASE_CLIENT_EMAIL'),
    'private_key' => str_replace('\n', "\n", env('FIREBASE_PRIVATE_KEY', '')),
    'storage_bucket' => env('FIREBASE_STORAGE_BUCKET'),

    'billing' => [
        'price_per_message' => (float) env('CHAT_PRICE_PER_MESSAGE', 5.00),
        'min_wallet_to_start' => (float) env('CHAT_MIN_WALLET_BALANCE', 50.00),
    ]
];
