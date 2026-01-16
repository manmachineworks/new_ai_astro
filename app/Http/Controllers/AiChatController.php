<?php

namespace App\Http\Controllers;

use App\Models\AiChatMessage;
use App\Models\AiChatSession;
use App\Services\AiService;
use App\Services\AstrologyApiService;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AiChatController extends Controller
{
    protected $aiService;
    protected $walletService;
    protected $astroApi;

    public function __construct(AiService $aiService, WalletService $walletService, AstrologyApiService $astroApi)
    {
        $this->aiService = $aiService;
        $this->walletService = $walletService;
        $this->astroApi = $astroApi;
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $user = $request->user();
        $pricePerMessage = 5.00; // Fixed price for AI chat msg

        // Check Balance
        if (!$this->walletService->hasBalance($user, $pricePerMessage)) {
            return response()->json(['message' => 'Insufficient balance'], 402);
        }

        // Get or Create Session
        $session = AiChatSession::firstOrCreate(
            ['user_id' => $user->id, 'status' => 'open'],
            [
                'per_chat_price' => $pricePerMessage,
                'total_cost' => 0
            ]
        );

        // Debit User
        $this->walletService->debit(
            $user,
            $pricePerMessage,
            'ai_chat',
            $session->id,
            "AI Message"
        );

        // Update Session Cost
        $session->increment('total_cost', $pricePerMessage);

        // Save User Message
        $userMsg = $session->messages()->create([
            'role' => 'user',
            'content' => $request->message,
        ]);

        // Generate AI Response
        $aiResponseContent = $this->aiService->generateResponse($session, $request->message);

        // Save AI Message
        $aiMsg = $session->messages()->create([
            'role' => 'assistant',
            'content' => $aiResponseContent,
        ]);

        return response()->json([
            'user_message' => $userMsg,
            'ai_message' => $aiMsg,
            'remaining_balance' => $user->fresh()->wallet_balance
        ]);
    }

    public function getHistory(Request $request)
    {
        $session = AiChatSession::where('user_id', $request->user()->id)
            ->where('status', 'open')
            ->first();

        if (!$session) {
            return response()->json([]);
        }

        return response()->json($session->messages);
    }
}
