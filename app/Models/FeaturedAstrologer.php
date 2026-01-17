<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class FeaturedAstrologer extends Model
{
    protected $fillable = [
        'astrologer_profile_id',
        'locale',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function astrologerProfile()
    {
        return $this->belongsTo(\App\Models\AstrologerProfile::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForLocale($query, $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $query->where('locale`, $locale);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    // Cache invalidation
    public static function boot()
    {
        parent::boot();

        $invalidateCache = function () {
            Cache::forget('featured_astrologers:en');
            Cache::forget('featured_astrologers:hi');
        };

        static::saved($invalidateCache);
        static::deleted($invalidateCache);
    }
}
