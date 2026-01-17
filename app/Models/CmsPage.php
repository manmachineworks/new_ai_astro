<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CmsPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'title',
        'content_html',
        'meta_title',
        'meta_description',
        'locale',
        'status',
        'published_at',
        'created_by_admin_id'
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    protected $keyType = 'string';
    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }
}
