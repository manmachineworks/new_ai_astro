<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ReferralCode extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'code',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate a unique referral code
     */
    public static function generateUniqueCode(): string
    {
        do {
            // Format: ASTRO + 4 random chars (e.g., ASTRO1A2B)
            $code = 'ASTRO' . strtoupper(Str::random(4));
        } while (self::where('code', $code)->exists());

        return $code;
    }



    /**
     * Create referral code for user
     */
    public static function createForUser(User $user): self
    {
        return self::create([
            'user_id' => $user->id,
            'code' => self::generateUniqueCode(),
        ]);
    }
}
