<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\ChatThread;
use App\Models\UserSecurityFlag;
use Illuminate\Http\Request;

class ChatModerationController extends Controller
{
    public function index(Request $request)
    {
        $query = ChatThread::with(['user', 'astrologer'])->orderByDesc('last_message_at');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                })->orWhereHas('astrologer', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('last_message_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('last_message_at', '<=', $request->date_to);
        }

        $threads = $query->paginate(20)->withQueryString();

        $selectedThread = null;
        $messages = collect();
        if ($request->filled('thread_id')) {
            $selectedThread = ChatThread::with(['user', 'astrologer'])->find($request->thread_id);
            if ($selectedThread) {
                $messages = ChatMessage::with('sender')
                    ->where('thread_id', $selectedThread->id)
                    ->orderBy('created_at')
                    ->limit(200)
                    ->get();
            }
        }

        $analytics = $this->buildAnalytics();

        return view('admin.moderation.chats.index', compact('threads', 'selectedThread', 'messages', 'analytics'));
    }

    public function mute(Request $request, ChatThread $thread)
    {
        $validated = $request->validate([
            'target' => 'required|in:user,astrologer',
            'duration_minutes' => 'nullable|integer|min:1',
            'reason' => 'nullable|string|max:500',
        ]);

        $userId = $validated['target'] === 'user'
            ? $thread->user_id
            : $thread->astrologer_user_id;

        $expiresAt = null;
        if (!empty($validated['duration_minutes'])) {
            $expiresAt = now()->addMinutes((int) $validated['duration_minutes']);
        }

        UserSecurityFlag::create([
            'user_id' => $userId,
            'flag_type' => 'chat_muted',
            'expires_at' => $expiresAt,
            'meta_json' => [
                'thread_id' => $thread->id,
                'target' => $validated['target'],
                'reason' => $validated['reason'] ?? null,
                'admin_id' => auth()->id(),
            ],
        ]);

        return back()->with('success', 'Chat access muted.');
    }

    public function unmute(Request $request, ChatThread $thread, string $target)
    {
        $userId = $target === 'user'
            ? $thread->user_id
            : $thread->astrologer_user_id;

        UserSecurityFlag::where('user_id', $userId)
            ->where('flag_type', 'chat_muted')
            ->where(function ($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->update(['expires_at' => now()]);

        return back()->with('success', 'Chat restriction lifted.');
    }

    private function buildAnalytics(): array
    {
        $since = now()->subDays(7);
        $messages = ChatMessage::where('created_at', '>=', $since)
            ->orderBy('thread_id')
            ->orderBy('created_at')
            ->get(['thread_id', 'sender_user_id', 'created_at']);

        $dailyCounts = [];
        $hourlyCounts = array_fill(0, 24, 0);
        $responseTotal = 0;
        $responseCount = 0;

        $lastByThread = [];

        foreach ($messages as $msg) {
            $day = $msg->created_at->format('Y-m-d');
            $dailyCounts[$day] = ($dailyCounts[$day] ?? 0) + 1;

            $hour = (int) $msg->created_at->format('H');
            $hourlyCounts[$hour]++;

            if (isset($lastByThread[$msg->thread_id])) {
                $prev = $lastByThread[$msg->thread_id];
                if ($prev['sender'] !== $msg->sender_user_id) {
                    $diff = $msg->created_at->diffInSeconds($prev['time']);
                    $responseTotal += $diff;
                    $responseCount++;
                }
            }

            $lastByThread[$msg->thread_id] = [
                'sender' => $msg->sender_user_id,
                'time' => $msg->created_at,
            ];
        }

        $avgResponse = $responseCount > 0 ? round($responseTotal / $responseCount) : null;

        arsort($hourlyCounts);
        $busiestHours = array_slice(array_keys($hourlyCounts), 0, 3);

        return [
            'daily_counts' => $dailyCounts,
            'avg_response_seconds' => $avgResponse,
            'busiest_hours' => $busiestHours,
        ];
    }
}
