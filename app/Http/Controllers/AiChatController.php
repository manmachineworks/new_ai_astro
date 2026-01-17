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
    protected $membershipService;

    public function __construct(AstrologyApiClient $client, WalletService $wallet, \App\Services\MembershipService $membershipService)
    {
        $this->client = $client;
        $this->wallet = $wallet;
        $this->membershipService = $membershipService;
    }

    // ... (index and start methods unchanged for now, assume free messages apply per message) ...

    public function sendMessage(Request $request, AiChatSession $session)
    {
        $this->authorizeAccess($session);

        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'client_message_id' => 'required|string|uuid'
        ]);

        // ... (Rate Limiting & Safety Filter unchanged) ...
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

        $baseAmount = $session->pricing_mode === 'per_message' ? (float) $session->price_per_message : 0;
        $amountToCharge = $baseAmount;
        $transactionId = null;
        $isFreeBenefit = false;

        // Try to consume free benefit
        if ($baseAmount > 0) {
            if ($this->membershipService->consumeBenefit(auth()->user(), 'ai_free_messages')) {
                $amountToCharge = 0;
                $isFreeBenefit = true;
            }
        }

        try {
            return DB::transaction(function () use ($session, $validated, $amountToCharge, $isFreeBenefit) {
                // 2. Idempotent Billing
                if ($amountToCharge > 0) {
                    $existing = AiMessageCharge::where('client_message_id', $validated['client_message_id'])->first();
                    if ($existing) {
                        return response()->json(['success' => true, 'note' => 'Already charged']);
                    }

                    if (auth()->user()->wallet_balance < $amountToCharge) {
                        return response()->json(['error' => 'Insufficient balance', 'redirect' => route('wallet.recharge')], 402);
                    }

                    $transaction = $this->wallet->debit(
                        auth()->user(),
                        $amountToCharge,
                        'ai_message_charge',
                        $validated['client_message_id'],
                        "AI Chat Message Charge",
                        ['client_message_id' => $validated['client_message_id'], 'session_id' => $session->id]
                    );

                    AiMessageCharge::create([
                        'ai_chat_session_id' => $session->id,
                        'client_message_id' => $validated['client_message_id'],
                        'amount' => $amountToCharge,
                        'wallet_transaction_id' => $transaction->id
                    ]);

                    $session->increment('total_charged', $amountToCharge);

                    // Update commission amount
                    $commAmt = ($amountToCharge * $session->commission_percent_snapshot) / 100;
                    $session->increment('commission_amount_total', $commAmt);
                } elseif ($isFreeBenefit) {
                    // Log benefit usage metadata if needed, already logged in MembershipService->consumeBenefit
                    // But maybe we want client_message_id link? 
                    // For MVP, MembershipEvent is enough.
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
                    if ($amountToCharge > 0) {
                        $this->wallet->credit(
                            auth()->user(),
                            $amountToCharge,
                            'ai_refund',
                            $userMsg->id,
                            "AI Chat Refund - Provider Error",
                            ['failed_message_id' => $userMsg->id]
                        );
                        $session->decrement('total_charged', $amountToCharge);
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
