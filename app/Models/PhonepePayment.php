<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhonepePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'merchant_txn_id',
        'phonepe_txn_id',
        'amount',
        'status',
        'type',
        'meta_json',
        'request_payload',
        'response_payload',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'meta_json' => 'array',
        'request_payload' => 'array',
        'response_payload' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
