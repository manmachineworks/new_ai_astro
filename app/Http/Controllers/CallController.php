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
    protected $membershipService;

    public function __construct(CallerDeskService $callerDesk, WalletService $walletService, \App\Services\MembershipService $membershipService)
    {
        $this->callerDesk = $callerDesk;
        $this->walletService = $walletService;
        $this->membershipService = $membershipService;
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

        if (!$profile) {
            return response()->json(['message' => 'Astrologer profile is unavailable for calls.'], 422);
        }

        // Check availability
        if (!$profile->is_call_enabled) {
            return response()->json(['message' => 'Astrologer is currently unavailable for calls'], 400);
        }

        if (!$profile->call_per_minute || $profile->call_per_minute <= 0) {
            return response()->json(['message' => 'Call pricing is not configured. Please try again later.'], 422);
        }

        // Pricing Gate: Min Balance
        $minWallet = \App\Models\PricingSetting::get('min_wallet_to_start_call', 50);
        if (!$this->walletService->hasBalance($user, $minWallet)) {
            return response()->json([
                'message' => "Insufficient wallet balance. You need at least ₹{$minWallet} to start a call."
            ], 402);
        }

        // Calculate Rate and Hold Amount
        $baseRate = $profile->call_per_minute;
        $discountData = $this->membershipService->calculateDiscount($user, 'call', $baseRate);
        $finalRate = $discountData['final_amount'];

        $holdDuration = \App\Models\PricingSetting::get('call_hold_duration_minutes', 5);
        $holdAmount = $finalRate * $holdDuration;

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
            'rate_per_minute' => $finalRate,
            'callerdesk_call_id' => $callId,
            'meta' => ['wallet_hold_id' => $hold->id, 'discount_applied' => $discountData['discount'] > 0 ? $discountData : null],
        ]);

        // Initiate Call
        $response = $this->callerDesk->initiateCall($user->phone, $astrologer->phone, $callId);

        if ($response) {
            $session->update([
                'status' => 'connecting',
                'meta' => ['api_response' => $response]
            ]);

            // Notify Astrologer
            \App\Jobs\SendPushNotificationJob::dispatch(
                $astrologer->id,
                'call_incoming',
                [
                    'call_session_id' => $callId,
                    'user_name' => "User #{$user->id}", // Masked Identity
                    'deeplink' => "app://calls/{$callId}"
                ],
                'Incoming Call',
                "New call request from User #{$user->id}"
            );

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
                'status' => $status === 'completed' ? 'completed' : ($status === 'active' ? 'active' : 'failed'),
                'duration_seconds' => $duration,
                'ended_at' => $status === 'completed' ? now() : null,
                'meta' => array_merge($session->meta ?? [], ['webhook' => $request->all()]),
            ]);

            // Call Started (Answered)
            if ($status === 'active' && $session->status !== 'active') { // Idempotency check
                \App\Jobs\SendPushNotificationJob::dispatch(
                    $session->user_id,
                    'call_started',
                    ['call_session_id' => $session->callerdesk_call_id, 'deeplink' => "app://calls/{$session->callerdesk_call_id}"],
                    'Call Connected',
                    "You are now connected with {$session->astrologer->name}."
                );
                return; // Exit transaction early for active state
            }

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

                // Notify User: Call Ended
                \App\Jobs\SendPushNotificationJob::dispatch(
                    $session->user_id,
                    'call_ended',
                    [
                        'call_session_id' => $session->callerdesk_call_id,
                        'duration' => (string) $mins,
                        'cost' => (string) $cost,
                        'deeplink' => "app://calls/{$session->callerdesk_call_id}/summary"
                    ],
                    'Call Summary',
                    "Call ended. Duration: {$mins} mins. Cost: INR {$cost}."
                );

            } else {
                // Failed/Busy/No Answer -> Release Hold
                if ($hold) {
                    $this->walletService->releaseHold($hold);
                }

                // Notify Astrologer: Missed Call (if busy/no-answer)
                if (in_array($status, ['busy', 'no-answer', 'failed'])) {
                    \App\Jobs\SendPushNotificationJob::dispatch(
                        $session->astrologer_user_id,
                        'call_missed',
                        [
                            'call_session_id' => $session->callerdesk_call_id,
                            'timestamp' => now()->toIso8601String(),
                            'deeplink' => "app://calls/history"
                        ],
                        'Missed Call',
                        "You missed a call from User #{$session->user_id}."
                    );
                }
            }
        });

        return response()->json(['status' => 'success']);
    }
}
