<?php

return [
    'default_timezone' => env('APPOINTMENTS_DEFAULT_TZ', 'Asia/Kolkata'),
    'slot_length_minutes' => (int) env('APPOINTMENTS_SLOT_LENGTH', 30),
    'generate_days' => (int) env('APPOINTMENTS_GENERATE_DAYS', 14),
    'hold_minutes' => (int) env('APPOINTMENTS_HOLD_MINUTES', 10),
    'pending_hold_minutes' => (int) env('APPOINTMENTS_PENDING_HOLD_MINUTES', 60),
    'auto_confirm' => env('APPOINTMENTS_AUTO_CONFIRM', false),
    'pricing' => [
        'mode' => env('APPOINTMENTS_PRICING_MODE', 'per_minute'), // fixed|per_minute
        'fixed_price' => (float) env('APPOINTMENTS_FIXED_PRICE', 0),
        'fallback_rate_per_minute' => (float) env('APPOINTMENTS_FALLBACK_RATE_PER_MINUTE', 100),
    ],
    'cancellation' => [
        'user_full_refund_hours' => (int) env('APPOINTMENTS_CANCEL_USER_FULL_HOURS', 6),
        'user_partial_refund_percent' => (int) env('APPOINTMENTS_CANCEL_USER_PARTIAL_REFUND_PERCENT', 50),
        'astrologer_refund_percent' => (int) env('APPOINTMENTS_CANCEL_ASTRO_REFUND_PERCENT', 100),
    ],
    'reminders' => [
        'lead_minutes' => [1440, 60, 10],
    ],
    'meeting' => [
        'enabled' => env('APPOINTMENTS_MEETING_ENABLED', true),
        'provider' => env('APPOINTMENTS_MEETING_PROVIDER', 'jitsi'),
        'jitsi_base_url' => env('APPOINTMENTS_JITSI_BASE_URL', 'https://meet.jit.si'),
        'reveal_minutes_before' => (int) env('APPOINTMENTS_MEETING_REVEAL_MINUTES', 10),
    ],
];
