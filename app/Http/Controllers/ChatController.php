<?php

namespace App\Http\Controllers;

use App\Models\ChatSession;
use App\Models\ChatMessageCharge;
use App\Models\AstrologerProfile;
use App\Services\FirebaseService;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    protected $firebase;
    protected $membershipService;

    public function __construct(FirebaseService $firebase, \App\Services\MembershipService $membershipService)
    {
        $this->firebase = $firebase;
        $this->membershipService = $membershipService;
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $sessions = ChatSession::with('astrologerProfile')
            ->where('user_id', $user->id)
            ->latest('updated_at')
            ->get();

        return view('user.chats.index', compact('sessions'));
    }

    public function astrologerIndex(Request $request)
    {
        $profile = $request->user()->astrologerProfile;
        $sessions = ChatSession::with('user')
            ->where('astrologer_profile_id', $profile->id)
            ->latest('updated_at')
            ->get();

        return view('astrologer.dashboard.chats', compact('sessions', 'profile'));
    }

    public function show(Request $request, $conversationId)
    {
        $session = ChatSession::where('conversation_id', $conversationId)
            ->with('astrologerProfile')
            ->firstOrFail();

        // Security
        if (
            $session->user_id !== $request->user()->id &&
            (!$request->user()->hasRole('Astrologer') || $session->astrologer_profile_id !== $request->user()->astrologerProfile->id)
        ) {
            abort(403);
        }

        return view('user.chats.show', compact('session'));
    }

    public function firebaseToken(Request $request)
    {
        $user = $request->user();
        $uid = "user_{$user->id}";

        if ($user->hasRole('Astrologer')) {
            $uid = "astro_{$user->astrologerProfile->id}";
        }

        $token = $this->firebase->createCustomToken($uid);

        return response()->json(['firebase_token' => $token, 'uid' => $uid]);
    }

    // start method modification
    public function start(Request $request)
    {
        $request->validate(['astrologer_id' => 'required|exists:astrologer_profiles,id']);
        $user = $request->user();
        $astroId = $request->astrologer_id;
        $astro = AstrologerProfile::findOrFail($astroId);

        // Security check
        if (!$astro->is_verified || !$astro->is_chat_enabled) {
            return response()->json(['error' => 'Astrologer not available for chat'], 400);
        }

        // Wallet check
        $minBalance = config('firebase.billing.min_wallet_to_start', 50);
        if ($user->wallet_balance < $minBalance) {
            return response()->json(['error' => 'Insufficient balance to start chat'], 402);
        }

        $session = ChatSession::where('user_id', $user->id)
            ->where('astrologer_profile_id', $astro->id)
            ->where('status', 'active')
            ->first();

        if (!$session) {
            // Calculate Price with Membership Discount
            $basePrice = $astro->chat_per_session > 0 ? $astro->chat_per_session : config('firebase.billing.price_per_message');
            $discountData = $this->membershipService->calculateDiscount($user, 'chat', $basePrice);
            $finalPrice = $discountData['final_amount'];

            $session = ChatSession::create([
                'user_id' => $user->id,
                'astrologer_profile_id' => $astro->id,
                'conversation_id' => 'conv_' . Str::random(20),
                'pricing_mode' => 'per_message',
                'price_per_message' => $finalPrice,
                'status' => 'active',
                'started_at' => now(),
            ]);

            // Should logging occur?
            // Initialization in Firestore would ideally happen here via service account
            // but rules allow participants to proceed if they have the ID.
        }

        return response()->json([
            'session_id' => $session->id,
            'conversation_id' => $session->conversation_id,
            'price_per_message' => $session->price_per_message
        ]);
    }

    /**
     * Gating: Check if user can send a message
     */
    public function authorizeSend(Request $request, $sessionId)
    {
        $session = ChatSession::findOrFail($sessionId);
        $user = $request->user();

        if ($session->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $price = $session->price_per_message;

        if ($user->wallet_balance < $price) {
            return response()->json(['allow' => false, 'message' => 'Low balance'], 402);
        }

        // Generate simple authorization token (HMAC signed)
        $msgClientId = $request->message_client_id ?: Str::random(10);
        $payload = "{$sessionId}:{$user->id}:{$msgClientId}:" . now()->addMinutes(5)->timestamp;
        $signature = hash_hmac('sha256', $payload, config('app.key'));

        return response()->json([
            'allow' => true,
            'auth_token' => "{$payload}.{$signature}",
            'charge_amount' => $price
        ]);
    }

    /**
     * Final Charge: Confirmation from client after Firestore write
     */
    public function confirmSent(Request $request, $sessionId)
    {
        $request->validate([
            'firestore_message_id' => 'required',
            'auth_token' => 'required'
        ]);

        $session = ChatSession::findOrFail($sessionId);

        // 1. Verify Auth Token
        [$payload, $receivedSignature] = explode('.', $request->auth_token);
        $expectedSignature = hash_hmac('sha256', $payload, config('app.key'));

        if (!hash_equals($expectedSignature, $receivedSignature)) {
            return response()->json(['error' => 'Invalid auth token'], 403);
        }

        // 2. Charging (Idempotent)
        try {
            return DB::transaction(function () use ($session, $request) {
                // Check if already charged
                if (ChatMessageCharge::where('firestore_message_id', $request->firestore_message_id)->exists()) {
                    return response()->json(['success' => true, 'message' => 'Already charged']);
                }

                $walletService = app(WalletService::class);
                $user = $session->user;
                $amount = $session->price_per_message;

                $transaction = $walletService->debit($user, $amount, "Chat Message Fee: #{$request->firestore_message_id}");

                ChatMessageCharge::create([
                    'chat_session_id' => $session->id,
                    'firestore_message_id' => $request->firestore_message_id,
                    'amount' => $amount,
                    'wallet_transaction_id' => $transaction->id
                ]);

                // Update session
                $session->increment('total_messages_user');
                $session->increment('total_charged', $amount);

                // Update commission
                $commAmt = ($amount * $session->commission_percent_snapshot) / 100;
                $session->increment('commission_amount_total', $commAmt);

                // Notifications Dispatch
                $recipientProfile = $session->astrologerProfile;
                $recipientUser = $recipientProfile->user;

                // 1. User Wallet Notifications are handled by WalletService::debit

                // 2. Notify Astrologer if User ran out of funds (Session Paused/Locked)
                if ($user->wallet_balance < $session->price_per_message) {
                    \App\Jobs\SendPushNotificationJob::dispatch(
                        $recipientUser->id,
                        'chat_session_paused',
                        [
                            'chat_session_id' => $session->id,
                            'conversation_id' => $session->conversation_id,
                            'user_name' => "User #{$user->id}",
                            'deeplink' => "app://chat/{$session->conversation_id}"
                        ],
                        'Chat Paused',
                        "User #{$user->id} has insufficient balance. Chat is paused."
                    );
                }

                return response()->json(['success' => true]);
            });
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
