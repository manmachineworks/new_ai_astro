<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'avatar',
        'wallet_balance',
        'is_active',
        'blocked_at',
        'blocked_until',
        'blocked_reason',
        'blocked_by_admin_id',
        'unblocked_at',
        'firebase_uid',
        'last_seen_at',
        'preferred_locale',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'wallet_balance' => 'decimal:2',
        'is_active' => 'boolean',
        'last_seen_at' => 'datetime',
        'blocked_at' => 'datetime',
        'blocked_until' => 'datetime',
        'unblocked_at' => 'datetime',
    ];


    public function preferences(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(UserPreference::class);
    }

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function memberships()
    {
        return $this->hasMany(UserMembership::class);
    }

    public function activeMembership()
    {
        return $this->hasOne(UserMembership::class)
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('ends_at_utc')
                    ->orWhere('ends_at_utc', '>', now());
            })
            ->latest('created_at'); // In case of duplicate actives, take newest
    }

    public function callSessions(): HasMany
    {
        return $this->hasMany(CallSession::class);
    }

    public function astrologerProfile()
    {
        return $this->hasOne(AstrologerProfile::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function deviceTokens(): HasMany
    {
        return $this->hasMany(DeviceToken::class);
    }

    public function referralCode()
    {
        return $this->hasOne(\App\Models\ReferralCode::class);
    }

    public function referralsAsInviter()
    {
        return $this->hasMany(\App\Models\Referral::class, 'inviter_user_id');
    }

    public function referralAsInvitee()
    {
        return $this->hasOne(\App\Models\Referral::class, 'invitee_user_id');
    }

    public function astrologer(): HasOne
    {
        return $this->hasOne(Astrologer::class);
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    public function walletTransactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function latestWalletTransaction()
    {
        return $this->hasOne(WalletTransaction::class)->latestOfMany();
    }

    public function callLogs(): HasMany
    {
        return $this->hasMany(CallLog::class);
    }

    public function chatSessions(): HasMany
    {
        return $this->hasMany(ChatSession::class);
    }

    public function publicId(): string
    {
        return 'USER-' . Str::padLeft((string) $this->id, 5, '0');
    }

    public function isAstrologer(): bool
    {
        return $this->role === 'astrologer' || $this->hasRole('Astrologer');
    }
}
