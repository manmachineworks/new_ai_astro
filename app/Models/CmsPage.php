<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CmsPage extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'content_html',
        'meta_title',
        'meta_description',
        'locale',
        'status',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeForLocale($query, $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $query->where('locale', $locale);
    }

    // Cache helpers
    public function getCacheKey()
    {
        return "cms_page:{$this->locale}:{$this->slug}";
    }

    public static function boot()
    {
        parent::boot();

        static::saved(function ($page) {
            Cache::forget($page->getCacheKey());
        });

        static::deleted(function ($page) {
            Cache::forget($page->getCacheKey());
        });
    }
}
