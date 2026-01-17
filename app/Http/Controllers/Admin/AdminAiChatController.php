<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiChatSession;
use Illuminate\Http\Request;

class AdminAiChatController extends Controller
{
    public function index(Request $request)
    {
        $query = AiChatSession::with('user');

        // Search
        if ($search = $request->input('search')) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
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

        // Stats Aggregation
        $statsQuery = clone $query;
        $aggregates = [
            'total_sessions' => $statsQuery->count(),
            'total_messages' => $statsQuery->sum('total_messages'),
            'total_revenue' => $statsQuery->sum('total_charged'),
            'total_commission' => $statsQuery->sum('commission_amount_total'),
        ];

        $sessions = $query->latest()->paginate(20)->withQueryString();

        return view('admin.ai.chats.index', compact('sessions', 'aggregates'));
    }

    public function show($id)
    {
        $session = AiChatSession::with([
            'user',
            'messages' => function ($q) {
                $q->orderBy('created_at', 'asc');
            }
        ])->findOrFail($id);

        return view('admin.ai.chats.show', compact('session'));
    }

    private function exportCsv($query)
    {
        $filename = 'ai_chats_export_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($query) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Date', 'User', 'Messages', 'Total Charged', 'Status']);

            $query->chunk(100, function ($sessions) use ($file) {
                foreach ($sessions as $session) {
                    fputcsv($file, [
                        $session->id,
                        $session->created_at,
                        $session->user->name ?? 'Deleted',
                        $session->total_messages,
                        $session->total_charged,
                        $session->status
                    ]);
                }
            });
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
