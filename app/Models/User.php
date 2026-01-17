<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

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
        'firebase_uid',
        'last_seen_at',
        'preferred_locale',
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
    ];


    public function preferences(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(UserPreference::class);
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
}
