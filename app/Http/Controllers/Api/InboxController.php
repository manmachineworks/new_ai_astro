<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class InboxController extends Controller
{
    /**
     * List notifications.
     * Query Params:
     * - status: 'unread' (default), 'all', 'archived'
     * - type: filter by type
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $status = $request->get('status', 'unread');

        $query = Notification::where('recipient_user_id', $user->id);

        if ($status === 'unread') {
            $query->where('status', 'unread');
        } elseif ($status === 'archived') {
            $query->where('status', 'archived');
        } else {
            // 'all' excludes archived usually, but let's say 'all' = unread + read
            $query->whereIn('status', ['unread', 'read']);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $notifications = $query->latest()
            ->paginate(20)
            ->through(function ($notif) {
                return [
                    'id' => $notif->id,
                    'type' => $notif->type,
                    'title' => $notif->title,
                    'body' => $notif->body,
                    'data' => $notif->data_json,
                    'status' => $notif->status,
                    'created_at' => $notif->created_at->toIso8601String(),
                    'read_at' => $notif->read_at?->toIso8601String(),
                ];
            });

        return response()->json($notifications);
    }

    public function markRead(Request $request, $id)
    {
        $notif = Notification::where('recipient_user_id', $request->user()->id)
            ->where('id', $id)
            ->firstOrFail();

        if ($notif->status === 'unread') {
            $notif->update([
                'status' => 'read',
                'read_at' => now()
            ]);
        }

        return response()->json(['message' => 'Marked as read']);
    }

    public function markAllRead(Request $request)
    {
        Notification::where('recipient_user_id', $request->user()->id)
            ->where('status', 'unread')
            ->update([
                'status' => 'read',
                'read_at' => now()
            ]);

        return response()->json(['message' => 'All marked as read']);
    }

    public function archive(Request $request, $id)
    {
        $notif = Notification::where('recipient_user_id', $request->user()->id)
            ->where('id', $id)
            ->firstOrFail();

        $notif->update([
            'status' => 'archived',
            'archived_at' => now()
        ]);

        return response()->json(['message' => 'Archived']);
    }
}
