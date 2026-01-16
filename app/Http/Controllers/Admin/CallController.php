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

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $calls = $query->latest()->paginate(20);

        return view('admin.calls.index', compact('calls'));
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
