<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AstrologerDocument extends Model
{
    protected $fillable = [
        'astrologer_profile_id',
        'doc_type',
        'file_path',
        'status',
        'reviewed_by_admin_id',
        'reviewed_at',
        'rejection_reason'
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function profile()
    {
        return $this->belongsTo(AstrologerProfile::class, 'astrologer_profile_id');
    }
}
