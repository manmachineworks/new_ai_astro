<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TrackingController extends Controller
{
    public function openInbox(Request $request, $id)
    {
        $user = $request->user();

        // Log "opened" event
        DB::table('notification_delivery_events')->insert([
            'id' => (string) Str::uuid(),
            'notification_id' => $id,
            'recipient_user_id' => $user->id,
            'channel' => 'inbox',
            'event' => 'opened',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Ensure marked as read too
        $notif = Notification::where('id', $id)->where('recipient_user_id', $user->id)->first();
        if ($notif && $notif->status === 'unread') {
            $notif->update(['status' => 'read', 'read_at' => now()]);
        }

        return response()->json(['message' => 'Tracked']);
    }

    public function openPush(Request $request)
    {
        $request->validate([
            'type' => 'nullable|string',
            'entity_id' => 'nullable|string',
            'notification_id' => 'nullable|string' // If provided by client from push payload
        ]);

        // Log push open
        DB::table('notification_delivery_events')->insert([
            'id' => (string) Str::uuid(),
            'notification_id' => $request->notification_id, // Might be null for legacy pushes
            'recipient_user_id' => $request->user()?->id, // Nullable if unauth route? But likely auth.
            'channel' => 'push',
            'event' => 'opened',
            'provider_message_id' => $request->input('message_id'), // Optional from FCM
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Tracked']);
    }
}
