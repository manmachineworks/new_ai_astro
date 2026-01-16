<?php

$credentials = env('FIREBASE_CREDENTIALS', 'app/firebase/firebase-admin.json');
$credentialsJson = env('FIREBASE_CREDENTIALS_JSON');
$credentials = ltrim($credentials, '\\/');
if (str_starts_with($credentials, 'storage/')) {
    $credentials = substr($credentials, strlen('storage/'));
}

return [
    'credentials' => storage_path($credentials),
    'credentials_json' => $credentialsJson,
    'project_id' => env('FIREBASE_PROJECT_ID', env('FIREBASE_WEB_PROJECT_ID')),

    'web' => [
        'api_key' => env('FIREBASE_WEB_API_KEY'),
        'auth_domain' => env('FIREBASE_WEB_AUTH_DOMAIN'),
        'project_id' => env('FIREBASE_WEB_PROJECT_ID'),
        'app_id' => env('FIREBASE_WEB_APP_ID'),
        'messaging_sender_id' => env('FIREBASE_WEB_MESSAGING_SENDER_ID'),
        'measurement_id' => env('FIREBASE_WEB_MEASUREMENT_ID'),
    ],
];
