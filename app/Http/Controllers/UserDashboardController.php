<?php

namespace App\Http\Controllers;

use App\Models\WalletTransaction;
use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $transactions = WalletTransaction::where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return view('user.dashboard', [
            'user' => $user,
            'balance' => $user->wallet_balance,
            'transactions' => $transactions
        ]);
    }
}
