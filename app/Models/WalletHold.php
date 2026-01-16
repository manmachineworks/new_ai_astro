<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class WalletHold extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'amount',
        'purpose',
        'status',
        'reference_type',
        'reference_id',
        'expires_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
