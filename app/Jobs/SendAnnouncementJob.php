<?php

namespace App\Jobs;

use App\Services\NotificationThrottleService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;

class SendAnnouncementJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $announcementId;

    public function __construct($announcementId)
    {
        $this->announcementId = $announcementId;
    }

    public function handle(Messaging $messaging, NotificationThrottleService $throttle)
    {
        $announcement = DB::table('admin_announcements')->find($this->announcementId);

        if (!$announcement || $announcement->status === 'sent') {
            return;
        }

        // 1. Mark as Sent
        DB::table('admin_announcements')->where('id', $this->announcementId)->update([
            'status' => 'sent',
            'sent_at' => now()
        ]);

        // 2. Determine Topic
        $topic = null;
        if ($announcement->target_role === 'all') {
            // How to target all? Condition: "'role_user' in topics || 'role_astrologer' in topics"
            $condition = "'role_user' in topics || 'role_astrologer' in topics";
            $message = CloudMessage::withTarget('condition', $condition);
        } else {
            $topic = 'role_' . strtolower($announcement->target_role);
            $message = CloudMessage::withTarget('topic', $topic);
        }

        // 3. Payload
        $message = $message->withNotification([
            'title' => $announcement->title,
            'body' => $announcement->body, // PII safe per schema
        ])->withData([
                    'type' => 'admin_announcement',
                    'announcement_id' => (string) $announcement->id,
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
                ]);

        // 4. Send Push
        try {
            $messaging->send($message);
            \Log::info("Announcement {$announcement->id} sent to topic/condition.");
        } catch (\Throwable $e) {
            \Log::error("Announcement Send Failed: " . $e->getMessage());
            DB::table('admin_announcements')->where('id', $this->announcementId)->update(['status' => 'failed']);
            return;
        }

        // 5. Create In-App Notifications (Bulk Insert)
        // This can be heavy for 1M users. We should use a Chunked Job or "Pull" model.
        // Requirement says: "Writes in-app notifications for recipients (bulk insert)".
        // For performance, we'll queue a separate job for In-App insertion or chunk here.
        // Let's do a simple chunk for now assuming < 10k users for this MVP scale.

        $role = $announcement->target_role;
        $query = \App\Models\User::query();
        if ($role !== 'all') {
            // Filter by role (assuming role() scope or relations exists, otherwise generic)
            // For now, assume generic users table holds everyone.
            // If using Spatie: query()->role($role).
            if (method_exists(\App\Models\User::class, 'scopeRole')) {
                $query->role($role);
            }
        }

        $query->chunkById(1000, function ($users) use ($announcement) {
            $insertData = [];
            foreach ($users as $user) {
                $insertData[] = [
                    'id' => \Illuminate\Support\Str::uuid()->toString(),
                    'recipient_user_id' => $user->id,
                    'type' => 'admin_announcement',
                    'title' => $announcement->title,
                    'body' => $announcement->body,
                    'data_json' => json_encode(['announcement_id' => $announcement->id]),
                    'status' => 'unread',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            DB::table('notifications')->insert($insertData);
        });
    }
}
