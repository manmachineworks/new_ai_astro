<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ExportRequest extends Model
{
    use HasUuids;

    protected $fillable = [
        'admin_id',
        'type',
        'filters_json',
        'status',
        'file_path',
        'error_message',
    ];

    protected $casts = [
        'filters_json' => 'array',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
