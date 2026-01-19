<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class AdminChatController extends Controller
{
    public function index(Request $request)
    {
        $query = ChatSession::with(['user', 'astrologer']); // ChatSession uses 'astrologer' relation name based on model view

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%");
                })->orWhereHas('astrologer', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
                });
            });
        }

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        if ($request->input('export') === 'csv') {
            return $this->exportCsv($query);
        }

        // Stats Aggregation
        $statsQuery = clone $query;
        $totalMessages = 0;
        if (Schema::hasColumn('chat_sessions', 'total_messages_user') && Schema::hasColumn('chat_sessions', 'total_messages_astrologer')) {
            $totalMessages = (int) (clone $statsQuery)
                ->selectRaw('SUM(COALESCE(total_messages_user, 0) + COALESCE(total_messages_astrologer, 0)) as total_messages')
                ->value('total_messages');
        } elseif (Schema::hasColumn('chat_sessions', 'messages_count')) {
            $totalMessages = (int) (clone $statsQuery)->sum('messages_count');
        }

        $aggregates = [
            'total_chats' => $statsQuery->count(),
            'total_minutes' => round($statsQuery->sum('duration_minutes'), 2),
            'total_revenue' => $statsQuery->sum('cost'), // Model uses 'cost'
            'total_commission' => $statsQuery->sum('commission_amount_total'),
            'total_astrologer_earning' => $statsQuery->sum('cost') - $statsQuery->sum('commission_amount_total'),
            'total_messages' => $totalMessages,
        ];

        $chats = $query->latest()->paginate(20)->withQueryString();

        return view('admin.chats.index', compact('chats', 'aggregates'));
    }

    public function show($id)
    {
        $chat = ChatSession::with(['user', 'astrologer', 'reports.reporter'])->findOrFail($id);

        return view('admin.chats.show', compact('chat'));
    }

    private function exportCsv($query)
    {
        $filename = 'chats_export_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($query) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Date', 'User', 'Astrologer', 'Duration', 'Cost', 'Commission', 'Status']);

            $query->chunk(100, function ($chats) use ($file) {
                foreach ($chats as $chat) {
                    fputcsv($file, [
                        $chat->id,
                        $chat->created_at,
                        $chat->user->name ?? 'Deleted',
                        $chat->astrologerProfile->display_name ?? 'Deleted',
                        $chat->duration_minutes,
                        $chat->gross_amount ?? $chat->amount,
                        $chat->commission_amount_total ?? 0,
                        $chat->status
                    ]);
                }
            });
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
