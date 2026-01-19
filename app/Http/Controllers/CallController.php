<?php

namespace App\Http\Controllers;

use App\Models\CallSession;
use App\Models\User;
use App\Models\AstrologerProfile;
use App\Models\PricingSetting;
use App\Models\WebhookEvent;
use App\Services\CallerDeskClient;
use App\Services\WalletService;
use App\Services\WebhookPayloadMasker;
use App\Jobs\ProcessCallerDeskWebhook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CallController extends Controller
{
    protected $callerDesk;
    protected $walletService;
    protected $membershipService;

    public function __construct(CallerDeskClient $callerDesk, WalletService $walletService, \App\Services\MembershipService $membershipService)
    {
        $this->callerDesk = $callerDesk;
        $this->walletService = $walletService;
        $this->membershipService = $membershipService;
    }

    public function initiate(Request $request)
    {
        $request->validate([
            'astrologer_id' => 'required',
        ]);

        $user = $request->user();
        $profile = AstrologerProfile::find($request->astrologer_id);
        if (!$profile) {
            $astrologer = User::role('Astrologer')
                ->where('id', $request->astrologer_id)
                ->with('astrologerProfile')
                ->firstOrFail();
            $profile = $astrologer->astrologerProfile;
        }

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
        $minWallet = (float) PricingSetting::get('min_wallet_to_start_call', 50);
        if (!$this->walletService->hasBalance($user, $minWallet)) {
            return response()->json([
                'message' => "Insufficient wallet balance. You need at least INR {$minWallet} to start a call."
            ], 402);
        }

        // Calculate Rate and Hold Amount
        $baseRate = $profile->call_per_minute;
        $discountData = $this->membershipService->calculateDiscount($user, 'call', $baseRate);
        $finalRate = $discountData['final_amount'];

        $holdDuration = (int) PricingSetting::get('call_hold_duration_minutes', 5);
        $holdAmount = $finalRate * $holdDuration;

        if (!$this->walletService->hasBalance($user, $holdAmount)) {
            return response()->json(['message' => "Insufficient wallet balance for initial {$holdDuration} mins"], 402);
        }

        // Create Session
        $callId = (string) Str::uuid();

        // 1. Create Wallet Hold
        try {
            $hold = $this->walletService->hold($user, $holdAmount, 'call_hold', 'call_session', $callId, $holdDuration + 5);
            // Expiry 5 mins buffer
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to secure funds'], 402);
        }

        $session = CallSession::create([
            'user_id' => $user->id,
            'astrologer_profile_id' => $profile->id,
            'provider' => 'callerdesk',
            'status' => 'initiated',
            'rate_per_minute' => $finalRate,
            'wallet_hold_id' => $hold->id,
            'user_masked_identifier' => 'User #' . substr((string) $user->id, -4),
            'astrologer_masked_identifier' => 'Astrologer #' . substr((string) $profile->id, -4),
            'commission_percent_snapshot' => (float) PricingSetting::get('platform_commission_percent', 20),
            'meta_json' => ['wallet_hold_id' => $hold->id, 'discount_applied' => $discountData['discount'] > 0 ? $discountData : null],
        ]);

        // Initiate Call
        try {
            $response = $this->callerDesk->initiateMaskedCall($user->phone, $profile->user->phone, [
                'call_session_id' => $session->id,
                'user_id' => $user->id,
                'astrologer_profile_id' => $profile->id,
            ]);
        } catch (\Exception $e) {
            $this->walletService->releaseHold($hold);
            $session->update(['status' => 'failed', 'meta_json' => array_merge($session->meta_json ?? [], ['error' => $e->getMessage()])]);
            return response()->json(['status' => 'error', 'message' => 'Failed to initiate call'], 502);
        }

        if ($response) {
            $session->update([
                'status' => 'connecting',
                'provider_call_id' => $response['provider_call_id'] ?? $callId,
                'meta_json' => array_merge($session->meta_json ?? [], ['api_response' => $response])
            ]);

            // Notify Astrologer
            \App\Jobs\SendPushNotificationJob::dispatch(
                $profile->user_id,
                'call_incoming',
                [
                    'call_session_id' => $session->id,
                    'user_name' => "User #{$user->id}", // Masked Identity
                    'deeplink' => "app://calls/{$session->id}"
                ],
                'Incoming Call',
                "New call request from User #{$user->id}"
            );

            return response()->json([
                'status' => 'success',
                'call_id' => $session->id,
                'provider_call_id' => $session->provider_call_id
            ]);
        }

        $session->update(['status' => 'failed']);
        return response()->json(['status' => 'error', 'message' => 'Failed to initiate call'], 500);
    }

    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $rawHeaders = $request->headers->all();
        $headers = WebhookPayloadMasker::mask($rawHeaders);
        $data = $request->all();
        $providerCallId = $data['call_id'] ?? ($data['provider_call_id'] ?? ($data['reference_id'] ?? null));

        $isValid = $this->callerDesk->verifyWebhookSignature($payload, $rawHeaders);

        $event = WebhookEvent::create([
            'provider' => 'callerdesk',
            'event_type' => $data['event'] ?? ($data['status'] ?? 'status_update'),
            'external_id' => $providerCallId,
            'signature_valid' => $isValid,
            'payload' => WebhookPayloadMasker::mask($data),
            'headers' => $headers,
            'processing_status' => $isValid ? 'pending' : 'failed',
            'error_message' => $isValid ? null : 'Invalid signature',
        ]);

        if (!$isValid) {
            return response()->json(['status' => 'received', 'warning' => 'invalid_signature']);
        }

        ProcessCallerDeskWebhook::dispatch($event->id);

        return response()->json(['status' => 'received', 'event_id' => $event->id]);
    }
}
