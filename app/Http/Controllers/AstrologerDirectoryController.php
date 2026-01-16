<?php

namespace App\Http\Controllers;

use App\Models\AstrologerProfile;
use Illuminate\Http\Request;

class AstrologerDirectoryController extends Controller
{
    public function index(Request $request)
    {
        $query = AstrologerProfile::where('is_verified', true)
            ->where('show_on_front', true)
            ->where('is_enabled', true)
            ->with(['user']); // Eager load user for name/photo

        // Filters
        if ($request->filled('skill')) {
            // JSON Search
            $query->whereJsonContains('skills', $request->skill);
        }

        if ($request->filled('language')) {
            $query->whereJsonContains('languages', $request->language);
        }

        if ($request->filled('min_rating')) {
            $query->where('rating_avg', '>=', $request->min_rating);
        }

        // Sorting
        if ($request->sort === 'price_asc') {
            $query->orderBy('call_per_minute', 'asc');
        } elseif ($request->sort === 'rating_desc') {
            $query->orderBy('rating_avg', 'desc');
        } else {
            // Default: Recommended / Rating
            $query->orderBy('rating_avg', 'desc');
        }

        $astrologers = $query->paginate(12);

        return view('astrologers.index', compact('astrologers'));
    }

    public function show($id)
    {
        $astrologer = AstrologerProfile::where('id', $id)
            ->where('is_verified', true)
            ->where('show_on_front', true)
            ->with(['user', 'availabilityRules', 'reviews.user'])
            ->firstOrFail();
        return view('astrologers.show', compact('astrologer'));
    }

    public function gate(Request $request, $id, $type)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => true, 'message' => 'Login required', 'redirect' => route('login')], 401);
        }

        $astrologer = AstrologerProfile::where('id', $id)
            ->where('is_verified', true)
            ->where('is_enabled', true)
            ->with('user')
            ->firstOrFail();

        // 1. Check Service Eligibility
        if ($type === 'call' && !$astrologer->is_call_enabled) {
            return response()->json(['error' => true, 'message' => 'Calls disabled for this astrologer'], 400);
        }
        if ($type === 'chat' && !$astrologer->is_chat_enabled) {
            return response()->json(['error' => true, 'message' => 'Chats disabled for this astrologer'], 400);
        }

        // 2. Check Wallet Balance via PricingSetting
        $settingKey = $type === 'call' ? 'min_wallet_to_start_call' : 'min_wallet_to_start_chat';
        $minBalance = \App\Models\PricingSetting::get($settingKey, 50);

        if ($user->wallet_balance < $minBalance) {
            return response()->json([
                'error' => true,
                'message' => "Insufficient balance. Minimum ₹{$minBalance} required.",
                'redirect' => route('wallet.recharge')
            ], 402);
        }

        // 3. Initiate Real Call / Chat
        if ($type === 'call') {
            return $this->handleCallInitiation($user, $astrologer);
        }

        if ($type === 'chat') {
            return $this->handleChatInitiation($user, $astrologer);
        }

        return response()->json(['success' => false, 'message' => 'Invalid service type']);
    }

    protected function handleChatInitiation($user, $astro)
    {
        // 1. Initial checks (Balance handled by ChatController too, but let's be safe)
        $minBalance = config('firebase.billing.min_wallet_to_start', 50);
        if ($user->wallet_balance < $minBalance) {
            return response()->json(['success' => false, 'message' => 'Insufficient balance to start chat'], 402);
        }

        // 2. Delegate to ChatController logic or implement here
        // Using ChatController@start logic
        $session = \App\Models\ChatSession::where('user_id', $user->id)
            ->where('astrologer_profile_id', $astro->id)
            ->where('status', 'active')
            ->first();

        if (!$session) {
            $session = \App\Models\ChatSession::create([
                'user_id' => $user->id,
                'astrologer_profile_id' => $astro->id,
                'conversation_id' => 'conv_' . \Illuminate\Support\Str::random(20),
                'pricing_mode' => 'per_message',
                'price_per_message' => $astro->chat_per_session > 0 ? $astro->chat_per_session : config('firebase.billing.price_per_message'),
                'status' => 'active',
                'started_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'redirect' => route('user.chats.show', $session->conversation_id)
        ]);
    }

    protected function handleCallInitiation($user, $astrologer)
    {
        $rate = $astrologer->call_per_minute;
        $holdMinutes = config('callerdesk.billing.minimum_hold_minutes', 5);
        $holdAmount = $rate * $holdMinutes;

        try {
            return \DB::transaction(function () use ($user, $astrologer, $rate, $holdAmount) {
                // 1. Create Wallet Hold
                $walletService = app(\App\Services\WalletService::class);
                $hold = $walletService->hold($user, $holdAmount, "Call reservation: {$astrologer->display_name}");

                // 2. Create Call Session
                $session = \App\Models\CallSession::create([
                    'user_id' => $user->id,
                    'astrologer_profile_id' => $astrologer->id,
                    'provider' => 'callerdesk',
                    'status' => 'initiated',
                    'rate_per_minute' => $rate,
                    'wallet_hold_id' => $hold->id,
                    'meta_json' => ['requested_at' => now()]
                ]);

                // 3. Call CallerDesk API
                $callerDesk = app(\App\Services\CallerDeskClient::class);
                // Note: In real app, phones are in users/profiles. Using placeholders if missing.
                $userPhone = $user->phone ?? '+919999999999';
                $astroPhone = $astrologer->user->phone ?? '+918888888888';

                $result = $callerDesk->initiateMaskedCall($userPhone, $astroPhone, [
                    'session_id' => $session->id,
                    'user_id' => $user->id
                ]);

                $session->update([
                    'provider_call_id' => $result['provider_call_id'],
                    'status' => $result['status'] ?? 'initiated'
                ]);

                return response()->json([
                    'success' => true,
                    'session_id' => $session->id,
                    'status' => $session->status,
                    'action' => 'call_initiated'
                ]);
            });
        } catch (\Exception $e) {
            \Log::error('Call Initiation failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => true, 'message' => 'Call system temporarily unavailable.'], 500);
        }
    }
}
