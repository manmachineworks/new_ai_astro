<?php

namespace App\Http\Controllers;

use App\Models\CallSession;
use App\Models\User;
use App\Services\CallerDeskService;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CallController extends Controller
{
    protected $callerDesk;
    protected $walletService;

    public function __construct(CallerDeskService $callerDesk, WalletService $walletService)
    {
        $this->callerDesk = $callerDesk;
        $this->walletService = $walletService;
    }

    public function initiate(Request $request)
    {
        $request->validate([
            'astrologer_id' => 'required|exists:users,id',
        ]);

        $user = $request->user();
        $astrologer = User::role('Astrologer')
            ->where('id', $request->astrologer_id)
            ->with('astrologerProfile')
            ->firstOrFail();

        $profile = $astrologer->astrologerProfile;

        // Check availability
        if (!$profile->is_call_enabled) {
            return response()->json(['message' => 'Astrologer is currently unavailable for calls'], 400);
        }

        // Pricing Gate: Min Balance
        $minWallet = \App\Models\PricingSetting::get('min_wallet_to_start_call', 50);
        if (!$this->walletService->hasBalance($user, $minWallet)) {
            return response()->json([
                'message' => "Insufficient wallet balance. You need at least ₹{$minWallet} to start a call."
            ], 402);
        }

        // Calculate Hold Amount (e.g. 5 mins)
        $rate = $profile->call_per_minute;
        $holdDuration = \App\Models\PricingSetting::get('call_hold_duration_minutes', 5);
        $holdAmount = $rate * $holdDuration;

        if (!$this->walletService->hasBalance($user, $holdAmount)) {
            return response()->json(['message' => "Insufficient wallet balance for initial {$holdDuration} mins"], 402);
        }

        // Create Session
        $callId = 'CALL_' . Str::uuid();

        // 1. Create Wallet Hold
        try {
            $hold = $this->walletService->hold($user, $holdAmount, 'call_hold', 'call_session', $callId, $holdDuration + 5);
            // Expiry 5 mins buffer
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to secure funds'], 402);
        }

        $session = CallSession::create([
            'user_id' => $user->id,
            'astrologer_user_id' => $astrologer->id,
            'status' => 'initiated',
            'rate_per_minute' => $rate,
            'callerdesk_call_id' => $callId,
            'meta' => ['wallet_hold_id' => $hold->id], // Store hold Ref
        ]);

        // Initiate Call
        $response = $this->callerDesk->initiateCall($user->phone, $astrologer->phone, $callId);

        if ($response) {
            $session->update([
                'status' => 'connecting',
                'meta' => ['api_response' => $response]
            ]);
            return response()->json(['status' => 'success', 'call_id' => $callId]);
        }

        $session->update(['status' => 'failed']);
        return response()->json(['status' => 'error', 'message' => 'Failed to initiate call'], 500);
    }

    public function webhook(Request $request)
    {
        // Example Payload: { reference_id, status, duration, start_time, end_time }
        // Adapt based on actual CallerDesk docs

        $callId = $request->reference_id;
        $status = $request->status; // completed, failed, busy, no-answer
        $duration = $request->duration ?? 0; // seconds

        $session = CallSession::where('callerdesk_call_id', $callId)->first();

        if (!$session) {
            return response()->json(['message' => 'Session not found'], 404);
        }

        if ($session->status === 'completed' || $session->status === 'failed') {
            return response()->json(['message' => 'Already processed']);
        }

        DB::transaction(function () use ($session, $status, $duration, $request) {
            $session->update([
                'status' => $status === 'completed' ? 'completed' : 'failed',
                'duration_seconds' => $duration,
                'ended_at' => now(),
                'meta' => array_merge($session->meta ?? [], ['webhook' => $request->all()]),
            ]);

            // Retrieve Hold
            $holdId = $session->meta['wallet_hold_id'] ?? null;
            $hold = $holdId ? \App\Models\WalletHold::find($holdId) : null;

            if ($status === 'completed' && $duration > 0) {
                $mins = ceil($duration / 60);
                $cost = $mins * $session->rate_per_minute;
                $session->update(['cost' => $cost]);

                if ($hold) {
                    // Consume Hold for exact cost amount
                    // If cost > hold, consumeHold handles the logic (up to hold amount + extra debit? Or simplified)
                    // Our service simplified logic: consume hold, release rest, debit shortage.
                    // The `consumeHold` logic I implemented does: if cost > hold, debit extra. If cost < hold, refund difference.
                    $this->walletService->consumeHold($hold, $cost);
                } else {
                    // Fallback if no hold (should not happen in new flow but safe to keep)
                    try {
                        $this->walletService->debit($session->user, $cost, 'call', $session->id, "Call with {$session->astrologer->name} ($mins mins)");
                    } catch (\Exception $e) { /* Log error */
                    }
                }

                // Credit Astrologer
                $commission = 0.70;
                $earning = $cost * $commission;
                $this->walletService->credit(
                    $session->astrologer,
                    $earning,
                    'call_earning',
                    $session->id,
                    "Earning from call with {$session->user->name}"
                );

            } else {
                // Failed/Busy/No Answer -> Release Hold
                if ($hold) {
                    $this->walletService->releaseHold($hold);
                }
            }
        });

        return response()->json(['status' => 'success']);
    }
}
