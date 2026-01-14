<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'avatar',
        'is_active',
        'phone_verified_at',
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // Relationships
    public function astrologerProfile()
    {
        return $this->hasOne(AstrologerProfile::class);
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function initiatedCalls()
    {
        return $this->hasMany(Call::class, 'user_id');
    }

    public function receivedCalls()
    {
        return $this->hasMany(Call::class, 'astrologer_id');
    }

    public function initiatedChats()
    {
        return $this->hasMany(ChatSession::class, 'user_id');
    }

    public function receivedChats()
    {
        return $this->hasMany(ChatSession::class, 'astrologer_id');
    }
}
