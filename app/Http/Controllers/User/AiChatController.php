<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AiChatController extends Controller
{
    public function index(Request $request)
    {
        if (!$this->checkBalance(15)) {
            return redirect()->route('user.wallet.recharge')
                ->with('error', 'Insufficient balance for AI Chat. Please recharge.');
        }

        $walletBalance = $request->user()->wallet_balance ?? 150.00;
        $pricePerQuery = 15.00;

        return view('user.ai.index', compact('walletBalance', 'pricePerQuery'));
    }

    public function send(Request $request)
    {
        // Mock response
        return response()->json([
            'success' => true,
            'reply' => 'This is a mock AI response based on your astrology query.'
        ]);
    }

    private function checkBalance($requiredAmount)
    {
        $user = auth()->user();
        $balance = $user->wallet_balance ?? 150.00;

        if ($balance < $requiredAmount) {
            return false;
        }
        return true;
    }
}
