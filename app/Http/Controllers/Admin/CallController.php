<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CallSession;
use App\Models\AstrologerProfile;
use Illuminate\Http\Request;

class CallController extends Controller
{
    public function index(Request $request)
    {
        $query = CallSession::with(['user', 'astrologerProfile.user']);

        // Search (User or Astrologer)
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%");
                })->orWhereHas('astrologerProfile', function ($sub) use ($search) {
                    $sub->where('display_name', 'like', "%{$search}%");
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
        if ($request->filled('astrologer_profile_id')) {
            $query->where('astrologer_profile_id', $request->input('astrologer_profile_id'));
        }

        if ($request->input('export') === 'csv') {
            return $this->exportCsv($query);
        }

        // Stats Aggregation (on filtered data)
        // using clone to avoid modifying the main query for pagination
        $statsQuery = clone $query;
        $aggregates = [
            'total_calls' => $statsQuery->count(),
            'total_minutes' => round($statsQuery->sum('billable_minutes'), 2),
            'total_revenue' => $statsQuery->sum('gross_amount'),
            'total_commission' => $statsQuery->sum('platform_commission_amount'),
            'total_astrologer_earning' => $statsQuery->sum('astrologer_earnings_amount'),
        ];

        $calls = $query->latest()->paginate(20)->withQueryString();

        $astrologers = AstrologerProfile::orderBy('display_name')->get();

        return view('admin.calls.index', compact('calls', 'aggregates', 'astrologers'));
    }

    private function exportCsv($query)
    {
        $filename = 'calls_export_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($query) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Date', 'User', 'Astrologer', 'Minutes', 'Gross', 'Commission', 'Status']);

            $query->chunk(100, function ($calls) use ($file) {
                foreach ($calls as $call) {
                    fputcsv($file, [
                        $call->id,
                        $call->created_at,
                        $call->user?->name ?? 'Deleted',
                        $call->astrologerProfile?->display_name ?? 'Deleted',
                        $call->billable_minutes,
                        $call->gross_amount,
                        $call->platform_commission_amount,
                        $call->status,
                    ]);
                }
            });
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function show($id)
    {
        $call = CallSession::with(['user', 'astrologerProfile.user', 'walletHold'])->findOrFail($id);

        // Fetch raw webhook events for audit
        $webhooks = \App\Models\WebhookEvent::where('provider', 'callerdesk')
            ->where('external_id', $call->provider_call_id)
            ->oldest()
            ->get();

        return view('admin.calls.show', compact('call', 'webhooks'));
    }
}
