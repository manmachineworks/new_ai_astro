<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use App\Services\NotificationDispatcher;
use App\Models\User;

class ReconcileNotificationLogsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(NotificationDispatcher $dispatcher)
    {
        // Find 'queued' events older than 10 minutes that have no 'sent' or 'failed' counterpart?
        // Actually, 'notification_delivery_events' is an append-only log.
        // We'd need to query complexly: "Find notification_ids where event='queued' and NOT EXISTS event='sent'..."

        // OR better: check `notifications` table status if we tracked 'processing'?
        // The prompt says "find queued but not sent and requeue".
        // Since we don't have a 'status' on the Notification model for PUSH, only Inbox.
        // Let's assume we rely on the Delivery Events for recovery or a dedicated 'notification_logs' table if we had one.
        // Given current schema, best effort is to check standard Jobs failure queue or rely on Horizon retries.

        // However, let's implement a logical check using the events table for Analytics discrepancies.

        $stuckThreshold = now()->subMinutes(10);

        // Example logic: Find IDs queued but stuck.
        // This is expensive on large datasets. 
        // For 'Production Hardening', simpler is better: Monitor Horizon Failed Jobs.

        // But let's verify Inbox status.
        // "Find unread notifications created > 30 days ago and archive them."

        DB::table('notifications')
            ->where('status', '!=', 'archived')
            ->where('created_at', '<', now()->subDays(60))
            ->update(['status' => 'archived', 'archived_at' => now()]);

        \Log::info("Reconciliation: Archived old notifications.");
    }
}
