<?php

return [
    // Time windows for filing disputes (in hours)
    'time_windows' => [
        'call' => 48, // 48 hours after call completion
        'chat' => 72, // 72 hours after last message
        'ai_chat' => 24, // 24 hours after session
        'appointment' => 24, // 24 hours after scheduled time
        'payment' => 72, // 72 hours after payment
    ],

    // Anti-abuse: Max disputes per user per day
    'max_disputes_per_day' => 3,

    // Auto-approve rules (optional, for future automation)
    'auto_approve_rules' => [
        'call' => [
            'connected_less_than_seconds' => 30,
            'charged_more_than_minutes' => 1,
            'max_auto_refund_amount' => 100.00,
        ],
    ],

    // Dispute reason codes
    'reason_codes' => [
        'poor_quality' => 'Poor service quality',
        'technical_issue' => 'Technical issues during service',
        'no_service' => 'Service not delivered',
        'overcharged' => 'Incorrect charges',
        'other' => 'Other reason',
    ],
];