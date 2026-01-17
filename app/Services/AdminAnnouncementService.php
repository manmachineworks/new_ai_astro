<?php

namespace App\Services;

use App\Jobs\SendAnnouncementJob;
use Illuminate\Support\Facades\DB;

class AdminAnnouncementService
{
    public function createAndSend(string $title, string $body, string $targetRole = 'all', ?array $segmentRules = null)
    {
        // 1. Create DB Record
        $id = DB::table('admin_announcements')->insertGetId([
            'title' => $title,
            'body' => $body,
            'target_role' => $targetRole,
            'segment_rules' => $segmentRules ? json_encode($segmentRules) : null,
            'status' => 'scheduled',
            'scheduled_at' => now(), // Assume immediate for now
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Dispatch Job
        SendAnnouncementJob::dispatch($id);

        return $id;
    }
}
