<?php

return [
    'base_url' => env('CALLERDESK_BASE_URL', 'https://api.callerdesk.io/v1'),
    'api_key' => env('CALLERDESK_API_KEY'),
    'api_secret' => env('CALLERDESK_API_SECRET'),
    'webhook_secret' => env('CALLERDESK_WEBHOOK_SECRET'),
    'masking_pool_id' => env('CALLERDESK_MASKING_POOL_ID'),

    'billing' => [
        'rounding_rule' => env('CALL_BILLING_ROUNDING_RULE', 'ceil'), // ceil, floor, none
        'minimum_hold_minutes' => (int) env('CALL_MINIMUM_HOLD_MINUTES', 5),
    ]
];
