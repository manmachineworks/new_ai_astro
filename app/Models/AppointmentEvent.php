<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentEvent extends Model
{
    public $timestamps = false; // Only created_at

    protected $fillable = [
        'appointment_id',
        'actor_type',
        'actor_id',
        'event_type',
        'meta_json',
        'created_at',
    ];

    protected $casts = [
        'meta_json' => 'array',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Get the actor (polymorphic - could be User, Admin, System)
     */
    public function actor()
    {
        if ($this->actor_type === 'system' || !$this->actor_id) {
            return null;
        }

        return User::find($this->actor_id);
    }
}
