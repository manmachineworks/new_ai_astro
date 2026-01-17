<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Faq extends Model
{
    protected $fillable = [
        'question',
        'answer_html',
        'locale',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForLocale($query, $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $query->where('locale', $locale);
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
            Cache::forget('faqs:en');
            Cache::forget('faqs:hi');
        };

        static::saved($invalidateCache);
        static::deleted($invalidateCache);
    }
}
