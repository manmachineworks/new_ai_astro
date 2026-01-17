<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CmsBanner extends Model
{
    protected $fillable = [
        'title',
        'image_path',
        'link_url',
        'position',
        'locale',
        'is_active',
        'sort_order',
        'start_at_utc',
        'end_at_utc',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_at_utc' => 'datetime',
        'end_at_utc' => 'datetime',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_at_utc')
                    ->orWhere('start_at_utc', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_at_utc')
                    ->orWhere('end_at_utc', '>=', now());
            });
    }

    public function scopeForLocale($query, $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $query->where('locale', $locale);
    }

    public function scopePosition($query, $position)
    {
        return $query->where('position', $position);
    }

    // Cache invalidation
    public static function boot()
    {
        parent::boot();

        $invalidateCache = function () {
            Cache::forget('cms_banners:en');
            Cache::forget('cms_banners:hi');
        };

        static::saved($invalidateCache);
        static::deleted($invalidateCache);
    }
}
