<?php

declare(strict_types=1);

return [
    'default' => 'app',

    'projects' => [
        'app' => [
            'credentials' => storage_path('app/firebase/firebase-admin.json'),

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
];
