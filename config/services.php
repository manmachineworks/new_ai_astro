<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'callerdesk' => [
        'api_key' => env('CALLERDESK_KEY'),
        'route' => env('CALLERDESK_ROUTE', 'inform'), // click2call or inform
        'url' => env('CALLERDESK_URL', 'https://api.callerdesk.io/v1'),
    ],

    'phonepe' => [
        'merchant_id' => env('PHONEPE_MERCHANT_ID', 'MERCHANTID'),
        'salt_key' => env('PHONEPE_SALT_KEY', 'salt-key'),
        'salt_index' => env('PHONEPE_SALT_INDEX', 1),
        'env' => env('PHONEPE_ENV', 'UAT'), // UAT or PROD
        'redirect_url' => env('APP_URL') . '/api/payment/redirect',
        'callback_url' => env('APP_URL') . '/api/webhooks/phonepe',
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'astrology_api' => [
        'base_url' => env('ASTROLOGY_API_URL', 'https://json.astrologyapi.com/v1'),
        'user_id' => env('ASTROLOGY_API_USER_ID'),
        'api_key' => env('ASTROLOGY_API_KEY'),
    ],
];
