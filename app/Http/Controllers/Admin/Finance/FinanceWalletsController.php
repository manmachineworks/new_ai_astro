<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceWalletsController extends Controller
{
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    public function index(Request $request)
    {
        $query = User::role('User')->with('latestWalletTransaction');

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%")
                ->orWhere('phone', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%");
        }

        if ($request->filled('sort')) {
            if ($request->sort === 'balance_high') {
                $query->orderByDesc('wallet_balance');
            } elseif ($request->sort === 'balance_low') {
                $query->orderBy('wallet_balance');
            }
        } else {
            $query->orderByDesc('updated_at');
        }

        $users = $query->paginate(20);

        return view('admin.finance.wallets.index', compact('users'));
    }

    public function show(User $user)
    {
        $transactions = $user->walletTransactions()->latest()->paginate(20);
        return view('admin.finance.wallets.show', compact('user', 'transactions'));
    }

    public function adjust(Request $request, User $user)
    {
        $request->validate([
            'type' => 'required|in:credit,debit',
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string|max:255',
        ]);

        try {
            if ($request->type === 'credit') {
                $this->walletService->credit(
                    $user,
                    $request->amount,
                    'system_adjustment',
                    auth()->id(), // Admin ID as Ref
                    $request->description
                );
            } else {
                $this->walletService->debit(
                    $user,
                    $request->amount,
                    'system_adjustment',
                    auth()->id(),
                    $request->description
                );
            }

            return back()->with('success', 'Wallet adjusted successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Adjustment failed: ' . $e->getMessage());
        }
    }
}
