<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_amount',
        'status',
        'payment_id',
        'shipping_address',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'shipping_address' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(ShopOrderItem::class);
    }
}
