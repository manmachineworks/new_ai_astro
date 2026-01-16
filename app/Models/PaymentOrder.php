<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PaymentOrder extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'provider',
        'merchant_transaction_id',
        'provider_transaction_id',
        'amount',
        'currency',
        'status',
        'payment_url',
        'redirect_url',
        'meta'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'meta' => 'json'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
