<?php

namespace App\Services;

use App\Models\AdminActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AdminActivityLogger
{
    public static function log(string $action, ?Model $target = null, array $metadata = []): AdminActivityLog
    {
        return AdminActivityLog::create([
            'causer_id' => Auth::id(),
            'action' => $action,
            'target_type' => $target?->getMorphClass(),
            'target_id' => $target?->getKey(),
            'metadata' => $metadata,
            'ip_address' => Request::ip(),
            'user_agent' => (string) Request::userAgent(),
        ]);
    }
}
