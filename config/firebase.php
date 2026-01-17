<?php

return [
    // Credentials
    'project_id' => env('FIREBASE_PROJECT_ID'),
    'credentials' => env('FIREBASE_CREDENTIALS'), // Path to JSON
    'client_email' => env('FIREBASE_CLIENT_EMAIL'),
    'private_key' => env('FIREBASE_PRIVATE_KEY'), // Optional if using JSON file

    // FCM Settings
    'fcm' => [
        'default_ttl' => (int) env('FCM_DEFAULT_TTL_SECONDS', 3600), // 1 hour
        'android_channels' => [
            'chat_messages' => [
                'id' => 'chat_channel',
                'name' => 'Chat Messages',
                'description' => 'Real-time chat notifications',
                'importance' => 'high'
            ],
            'calls' => [
                'id' => 'calls_channel',
                'name' => 'Incoming Calls',
                'description' => 'Alerts for incoming calls',
                'importance' => 'max'
            ],
            'wallet' => [
                'id' => 'wallet_channel',
                'name' => 'Wallet Alerts',
                'description' => 'Low balance and recharge updates',
                'importance' => 'default'
            ]
        ],
        'apns_headers' => [
            'apns-priority' => '10', // 10 = Immediate
        ]
    ],

    // Client SDK (Web)
    'api_key' => env('FIREBASE_API_KEY'),
    'auth_domain' => env('FIREBASE_AUTH_DOMAIN'),
    'messaging_sender_id' => env('FIREBASE_MESSAGING_SENDER_ID'),
    'app_id' => env('FIREBASE_APP_ID'),

    'billing' => [
        'price_per_message' => (float) env('CHAT_PRICE_PER_MESSAGE', 5.00),
        'min_wallet_to_start' => (float) env('CHAT_MIN_WALLET_BALANCE', 50.00),
    ]
];
