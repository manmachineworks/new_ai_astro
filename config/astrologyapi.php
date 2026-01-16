<?php

return [
    'base_url' => env('ASTROLOGYAPI_BASE_URL', 'https://json.astrologyapi.com/v1/'),
    'user_id' => env('ASTROLOGYAPI_USER_ID'),
    'api_key' => env('ASTROLOGYAPI_API_KEY'),
    'timeout' => env('ASTROLOGYAPI_TIMEOUT', 15),

    'limits' => [
        'daily_limit' => env('AI_CHAT_DAILY_LIMIT', 50),
        'rate_limit_per_min' => env('AI_CHAT_RATE_LIMIT_PER_MIN', 10),
    ],
];
