<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CallSession;
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

        return view('admin.calls.index', compact('calls', 'aggregates'));
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
