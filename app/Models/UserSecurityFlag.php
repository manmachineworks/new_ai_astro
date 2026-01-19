<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSecurityFlag extends Model
{
    protected $fillable = [
        'user_id',
        'flag_type',
        'expires_at',
        'meta_json',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'meta_json' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function hasActiveFlag(int $userId, string $flagType): bool
    {
        return self::query()
            ->where('user_id', $userId)
            ->where('flag_type', $flagType)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->exists();
    }
}
