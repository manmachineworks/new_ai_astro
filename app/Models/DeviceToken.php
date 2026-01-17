<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'role',
        'platform',
        'fcm_token',
        'device_id',
        'app_version',
        'locale',
        'is_enabled',
        'last_seen_at'
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'last_seen_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
