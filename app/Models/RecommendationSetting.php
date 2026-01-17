<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecommendationSetting extends Model
{
    protected $fillable = [
        'key',
        'value_json',
    ];

    protected $casts = [
        'value_json' => 'array',
    ];
}
