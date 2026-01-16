<?php

return [
    'api_key' => env('CALLERDESK_API_KEY'),
    'base_url' => env('CALLERDESK_BASE_URL'),
    'webhook_secret' => env('CALLERDESK_WEBHOOK_SECRET'),
    'timeout' => (int) env('CALLERDESK_TIMEOUT', 10),
];
