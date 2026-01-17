<?php

return [
    'notifications' => [
        'enabled' => env('FEATURE_NOTIFICATIONS_ENABLED', true),
        'push_enabled' => env('FEATURE_PUSH_ENABLED', true),
        'inbox_enabled' => env('FEATURE_INBOX_ENABLED', true),
        'chat_push_via_cloud_function' => env('FEATURE_CHAT_PUSH_CF', true),
    ],
    'billing' => [
        'enabled' => env('FEATURE_BILLING_ENABLED', true),
    ],
];
