<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeaturedAstrologer extends Model
{
    use HasFactory;

    protected $fillable = [
        'astrologer_profile_id',
        'locale',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function profile()
    {
        return $this->belongsTo(AstrologerProfile::class, 'astrologer_profile_id');
    }
}
