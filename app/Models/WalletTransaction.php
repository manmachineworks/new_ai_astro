<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'amount',
        'type',
        'reference_type',
        'reference_id',
        'description',
        'balance_after',
        'meta',
        'transaction_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'meta' => 'json',
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}
