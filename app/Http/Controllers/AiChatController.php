<?php

namespace App\Http\Controllers;

use App\Models\AiChatSession;
use App\Models\AiChatMessage;
use App\Models\AiMessageCharge;
use App\Models\AiChatReport;
use App\Models\PricingSetting;
use App\Services\AstrologyApiClient;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AiChatController extends Controller
{
    protected $client;
    protected $wallet;

    public function __construct(AstrologyApiClient $client, WalletService $wallet)
    {
        $this->client = $client;
        $this->wallet = $wallet;
    }

    public function index()
    {
        $sessions = AiChatSession::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);
        return view('user.ai_chat.index', compact('sessions'));
    }

    public function start(Request $request)
    {
        // 1. Check if AI Chat is enabled
        if (!PricingSetting::get('ai_chat_enabled', true)) {
            return back()->with('error', 'AI Chat is currently disabled by administrator.');
        }

        // 2. Check Wallet
        $minWallet = (float) PricingSetting::get('ai_chat_min_wallet_to_start', 50.00);
        if (auth()->user()->wallet_balance < $minWallet) {
            return redirect()->route('wallet.recharge')->with('error', "Insufficient balance. Minimum ₹{$minWallet} required to start AI Chat.");
        }

        // 3. Pricing Snapshot
        $mode = PricingSetting::get('ai_chat_pricing_mode', 'per_message');
        $pricePerMsg = (float) PricingSetting::get('ai_chat_price_per_message', 5.00);
        $sessionPrice = (float) PricingSetting::get('ai_chat_price_per_session', 100.00);
        $commPercent = (float) PricingSetting::get('ai_chat_platform_commission_percent', 10.00);

        try {
            return DB::transaction(function () use ($mode, $pricePerMsg, $sessionPrice, $commPercent) {
                $session = AiChatSession::create([
                    'user_id' => auth()->id(),
                    'pricing_mode' => $mode,
                    'price_per_message' => $pricePerMsg,
                    'session_price' => $sessionPrice,
                    'commission_percent_snapshot' => $commPercent,
                    'status' => 'active',
                    'started_at' => now(),
                ]);

                // 4. If per_session, debit immediately
                if ($mode === 'per_session') {
                    $this->wallet->debit(
                        auth()->user(),
                        $sessionPrice,
                        'ai_chat_charge',
                        $session->id,
                        "AI Chat Session Charge",
                        ['ai_session_id' => $session->id]
                    );
                    $session->increment('total_charged', $sessionPrice);

                    // Update total commission if per_session
                    $commAmt = ($sessionPrice * $commPercent) / 100;
                    $session->update(['commission_amount_total' => $commAmt]);
                }

                // Initial system message
                AiChatMessage::create([
                    'ai_chat_session_id' => $session->id,
                    'role' => 'system',
                    'content' => PricingSetting::get('ai_chat_disclaimer_text', 'AI Astrology Consultation.')
                ]);

                return redirect()->route('user.ai_chat.show', $session->id);
            });
        } catch (\Exception $e) {
            Log::error("Failed to start AI chat session", ['error' => $e->getMessage()]);
            return back()->with('error', 'Could not initiate AI chat. Please try again.');
        }
    }

    public function show(AiChatSession $session)
    {
        $this->authorizeAccess($session);
        $messages = $session->messages()->orderBy('created_at', 'asc')->get();
        return view('user.ai_chat.show', compact('session', 'messages'));
    }

    public function sendMessage(Request $request, AiChatSession $session)
    {
        $this->authorizeAccess($session);

        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'client_message_id' => 'required|string|uuid'
        ]);

        // 1. Rate Limiting (Simple Daily Check)
        $dailyLimit = (int) PricingSetting::get('ai_chat_max_messages_per_day', 50);
        $count = AiChatMessage::whereHas('session', function ($q) {
            $q->where('user_id', auth()->id());
        })->where('role', 'user')
            ->whereDate('created_at', now()->toDateString())
            ->count();

        if ($count >= $dailyLimit) {
            return response()->json(['error' => 'Daily message limit reached.'], 429);
        }

        // 1.5 Basic Safety Filter
        $harmfulPatterns = ['/suicide/i', '/kill myself/i', '/hurt myself/i', '/bomb/i', '/terror/i'];
        foreach ($harmfulPatterns as $pattern) {
            if (preg_match($pattern, $validated['message'])) {
                Log::warning("Harmful content detected in AI chat", ['user_id' => auth()->id(), 'message' => $validated['message']]);
                return response()->json(['error' => 'Your message was flagged by our safety system. Please refrain from sending harmful content.'], 403);
            }
        }

        $amount = $session->pricing_mode === 'per_message' ? (float) $session->price_per_message : 0;

        try {
            return DB::transaction(function () use ($session, $validated, $amount) {
                // 2. Idempotent Billing
                if ($amount > 0) {
                    $existing = AiMessageCharge::where('client_message_id', $validated['client_message_id'])->first();
                    if ($existing) {
                        return response()->json(['success' => true, 'note' => 'Already charged']);
                    }

                    if (auth()->user()->wallet_balance < $amount) {
                        return response()->json(['error' => 'Insufficient balance', 'redirect' => route('wallet.recharge')], 402);
                    }

                    $transaction = $this->wallet->debit(
                        auth()->user(),
                        $amount,
                        'ai_message_charge',
                        $validated['client_message_id'],
                        "AI Chat Message Charge",
                        ['client_message_id' => $validated['client_message_id'], 'session_id' => $session->id]
                    );

                    AiMessageCharge::create([
                        'ai_chat_session_id' => $session->id,
                        'client_message_id' => $validated['client_message_id'],
                        'amount' => $amount,
                        'wallet_transaction_id' => $transaction->id
                    ]);

                    $session->increment('total_charged', $amount);

                    // Update commission amount
                    $commAmt = ($amount * $session->commission_percent_snapshot) / 100;
                    $session->increment('commission_amount_total', $commAmt);
                }

                // 3. Store User Message
                $userMsg = AiChatMessage::create([
                    'ai_chat_session_id' => $session->id,
                    'role' => 'user',
                    'content' => $validated['message']
                ]);
                $session->increment('total_messages');

                // 4. Get History for AI
                $history = $session->messages()
                    ->whereIn('role', ['user', 'assistant', 'system'])
                    ->orderBy('created_at', 'desc')
                    ->limit(11) // Last 10 + current
                    ->get()
                    ->reverse()
                    ->map(fn($m) => ['role' => $m->role, 'content' => $m->content])
                    ->toArray();

                try {
                    // 5. Call AstrologyAPI
                    $response = $this->client->aiChat($history);
                    $aiContent = $response['prediction'] ?? ($response['message'] ?? 'Unable to process request.');

                    $aiMsg = AiChatMessage::create([
                        'ai_chat_session_id' => $session->id,
                        'role' => 'assistant',
                        'content' => $aiContent,
                        'provider_message_id' => $response['request_id'] ?? null
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => $aiMsg,
                        'balance' => auth()->user()->wallet_balance
                    ]);

                } catch (\Exception $e) {
                    // 6. Auto-Refund on Provider Failure
                    if ($amount > 0) {
                        $this->wallet->credit(
                            auth()->user(),
                            $amount,
                            'ai_refund',
                            $userMsg->id,
                            "AI Chat Refund - Provider Error",
                            ['failed_message_id' => $userMsg->id]
                        );
                        $session->decrement('total_charged', $amount);
                    }

                    Log::error("AstrologyAPI failed for AI chat", ['error' => $e->getMessage()]);
                    return response()->json(['error' => 'Astrology service is currently unavailable. Your wallet has been refunded.'], 502);
                }
            });
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong. ' . $e->getMessage()], 500);
        }
    }

    public function reportMessage(Request $request, $id)
    {
        $message = AiChatMessage::findOrFail($id);
        $this->authorizeAccess($message->session);

        $validated = $request->validate([
            'reason' => 'required|string|max:100',
            'details' => 'nullable|string|max:500'
        ]);

        AiChatReport::create([
            'user_id' => auth()->id(),
            'ai_chat_message_id' => $message->id,
            'reason' => $validated['reason'],
            'details' => $validated['details']
        ]);

        return response()->json(['success' => true, 'message' => 'Report submitted.']);
    }

    protected function authorizeAccess(AiChatSession $session)
    {
        if ($session->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
