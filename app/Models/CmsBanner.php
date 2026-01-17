<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CmsBanner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'image_path',
        'link_url',
        'position',
        'locale',
        'is_active',
        'sort_order',
        'start_at_utc',
        'end_at_utc'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_at_utc' => 'datetime',
        'end_at_utc' => 'datetime',
    ];
}
