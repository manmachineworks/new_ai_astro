<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingSetting extends Model
{
    protected $fillable = ['key', 'value_json', 'updated_by_admin_id'];

    protected $casts = [
        'value_json' => 'json',
    ];

    /**
     * Helper to get a value by key with a default fallback
     */
    public static function get(string $key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        // The value is stored in 'value_json', but the accessor or cast handles it.
        // However, if we saved it as json_encode($val), it comes out native type via cast.
        if (!$setting) {
            return $default;
        }

        return $setting->value_json !== null ? $setting->value_json : $default;
    }
}
