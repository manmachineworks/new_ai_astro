<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'firebase_uid',
        'is_active',
        'wallet_balance',
        'password',
    ];

    /**
     * The guard name for Spatie permissions.
     *
     * @var string
     */
    protected $guard_name = 'web';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
        'password' => 'hashed',
    ];

    public function userProfile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function astrologerProfile(): HasOne
    {
        return $this->hasOne(AstrologerProfile::class);
    }

    public function walletAccount(): HasOne
    {
        return $this->hasOne(WalletAccount::class);
    }

    public function walletTransactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function phonepePayments(): HasMany
    {
        return $this->hasMany(PhonepePayment::class);
    }

    public function callSessions(): HasMany
    {
        return $this->hasMany(CallSession::class, 'user_id');
    }

    public function callSessionsAsAstrologer(): HasMany
    {
        return $this->hasMany(CallSession::class, 'astrologer_user_id');
    }

    public function chatThreads(): HasMany
    {
        return $this->hasMany(ChatThread::class, 'user_id');
    }

    public function chatThreadsAsAstrologer(): HasMany
    {
        return $this->hasMany(ChatThread::class, 'astrologer_user_id');
    }

    public function chatMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'sender_user_id');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'user_id');
    }

    public function appointmentsAsAstrologer(): HasMany
    {
        return $this->hasMany(Appointment::class, 'astrologer_user_id');
    }

    public function availabilityRules(): HasMany
    {
        return $this->hasMany(AvailabilityRule::class, 'astrologer_user_id');
    }

    public function aiChatSessions(): HasMany
    {
        return $this->hasMany(AiChatSession::class, 'user_id');
    }

    public function aiChatSessionsAsAstrologer(): HasMany
    {
        return $this->hasMany(AiChatSession::class, 'astrologer_user_id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'actor_user_id');
    }
}
