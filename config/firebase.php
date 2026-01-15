<?php

declare(strict_types=1);

return [
    'default' => 'app',

    'projects' => [
        'app' => [
            'credentials' => env('FIREBASE_CREDENTIALS', storage_path('app/firebase/firebase-admin.json')),

            'auth' => [
                'tenant_id' => env('FIREBASE_AUTH_TENANT_ID'),
            ],

            'firestore' => [],
            'database' => [],
            'storage' => [
                'default_bucket' => env('FIREBASE_STORAGE_DEFAULT_BUCKET'),
            ],
            'cache_store' => 'file',
            'logging' => [
                'http_log_channel' => null,
                'http_debug_log_channel' => null,
            ],
            'http_client_options' => [
                'proxy' => null,
                'timeout' => null,
            ],
        ],
    ],

    'web' => [
        'api_key' => env('FIREBASE_WEB_API_KEY'),
        'auth_domain' => env('FIREBASE_WEB_AUTH_DOMAIN'),
        'project_id' => env('FIREBASE_WEB_PROJECT_ID'),
        'app_id' => env('FIREBASE_WEB_APP_ID'),
        'messaging_sender_id' => env('FIREBASE_WEB_MESSAGING_SENDER_ID'),
        'measurement_id' => env('FIREBASE_WEB_MEASUREMENT_ID'),
    ],
];
