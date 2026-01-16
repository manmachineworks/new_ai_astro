<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhonepePayment extends Model
{
    protected $fillable = [
        'user_id',
        'merchant_txn_id',
        'phonepe_txn_id',
        'amount',
        'status',
        'request_payload',
        'response_payload',
    ];

    protected $casts = [
        'amount' => 'integer',
        'request_payload' => 'array',
        'response_payload' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
