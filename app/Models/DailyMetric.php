<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyMetric extends Model
{
    protected $fillable = [
        'date_ist',
        'call_gross',
        'call_commission',
        'call_earnings',
        'chat_gross',
        'chat_commission',
        'chat_earnings',
        'ai_gross',
        'ai_commission',
        'ai_earnings',
        'wallet_recharge_success',
        'wallet_recharge_count_success',
        'wallet_recharge_count_failed',
        'new_users',
        'active_users',
    ];

    protected $casts = [
        'date_ist' => 'date',
        'call_gross' => 'decimal:2',
        'call_commission' => 'decimal:2',
        'call_earnings' => 'decimal:2',
        'chat_gross' => 'decimal:2',
        'chat_commission' => 'decimal:2',
        'chat_earnings' => 'decimal:2',
        'ai_gross' => 'decimal:2',
        'ai_commission' => 'decimal:2',
        'ai_earnings' => 'decimal:2',
        'wallet_recharge_success' => 'decimal:2',
    ];
}
