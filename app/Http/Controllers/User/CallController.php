<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\CallSession;
use App\Models\AstrologerProfile;
use Illuminate\Http\Request;

class CallController extends Controller
{
    public function index()
    {
        $calls = CallSession::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->paginate(10)
            ->through(function ($call) {
                $astro = $call->astrologerProfile;
                // Transform to View expectation or update view to use model
                // View 'user.calls.index' expects array keys: astrologer_name, date, duration, etc.
                return [
                    'id' => $call->id,
                    'astrologer_name' => $astro->display_name ?? 'Unknown',
                    'astrologer_image' => $astro->profile_photo_path ?? null,
                    'date' => $call->created_at->format('M d, Y'),
                    'time' => $call->created_at->format('h:i A'),
                    'duration' => floor(($call->duration_seconds ?? 0) / 60) . ' mins',
                    'status' => $call->status, // completed, missed, etc.
                    'cost' => $call->gross_amount ?? 0.00
                ];
            });

        return view('user.calls.index', compact('calls'));
    }

    public function dial(Request $request, $astrologerId)
    {
        $astrologer = AstrologerProfile::findOrFail($astrologerId);

        // Dynamic Guardrail: 5-minute buffer requirement?
        // Let's assume minimum 5 mins * rate_per_min
        $minMinutes = 5;
        $rate = $astrologer->call_per_minute ?? 0;
        $requiredAmount = $rate * $minMinutes;

        if (!$this->checkBalance($requiredAmount)) {
            return redirect()->route('user.wallet.recharge')
                ->with('error', "Insufficient balance. You need at least ₹{$requiredAmount} for 5 mins.");
        }

        $astrologerName = $astrologer->display_name;

        // Logic to initiate real call API would go here or via AJAX in validation step
        // For now, we show the 'Dialing' UI which initiates the connection
        return view('user.calls.dial', compact('astrologerName', 'astrologerId'));
    }

    public function summary($callId)
    {
        $session = CallSession::where('user_id', auth()->id())
            ->where('id', $callId)
            ->firstOrFail();

        $call = [
            'id' => $session->id,
            'astrologer_name' => $session->astrologerProfile->display_name ?? 'Astrologer',
            'duration' => floor(($session->duration_seconds ?? 0) / 60) . ' mins',
            'cost' => number_format($session->gross_amount ?? 0, 2),
            'status' => $session->status
        ];

        return view('user.calls.summary', compact('call'));
    }

    private function checkBalance($requiredAmount)
    {
        $user = auth()->user();
        $balance = $user->wallet_balance ?? 0.00;
        return $balance >= $requiredAmount;
    }
}
