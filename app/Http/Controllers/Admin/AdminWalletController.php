<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\WalletService; // Assuming this service exists or we will use direct model for now if service is complex
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\AdminActivityLogger;

class AdminWalletController extends Controller
{
    public function index(Request $request)
    {
        $query = User::select('users.*'); // Users with wallet balance

        // Filter by Role (User/Astrologer) - Optional, but useful
        if ($request->filled('role')) {
            $query->role($request->role);
        }

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Balance Range
        if ($request->filled('min_balance')) {
            $query->where('wallet_balance', '>=', $request->min_balance);
        }

        $users = $query->orderBy('wallet_balance', 'desc')->paginate(20)->withQueryString();

        return view('admin.wallets.index', compact('users'));
    }

    // Show wallet history for a specific user
    public function show($userId)
    {
        $user = User::findOrFail($userId);
        $transactions = WalletTransaction::where('user_id', $userId)
            ->latest()
            ->paginate(20);

        return view('admin.wallets.show', compact('user', 'transactions'));
    }

    public function recharge(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'type' => 'required|in:credit,debit',
            'reason' => 'required|string|min:5',
            'reference_id' => 'nullable|string|max:50',
            'idempotency_key' => 'nullable|string|size:36' // UUID
        ]);

        // Idempotency Check
        if ($request->filled('idempotency_key')) {
            $existing = \App\Models\AdminActivityLog::where('action', 'wallet_adjustment')
                ->whereJsonContains('metadata->idempotency_key', $request->idempotency_key)
                ->exists();

            if ($existing) {
                return back()->with('error', 'This specific transaction has already been processed (Idempotency Check).');
            }
        }

        $user = User::findOrFail($id);

        DB::transaction(function () use ($user, $request) {
            $user = User::lockForUpdate()->find($user->id); // Lock row
            $amount = $request->amount;
            $oldBalance = $user->wallet_balance;

            if ($request->type === 'credit') {
                $user->increment('wallet_balance', $amount);
                $newBalance = $user->wallet_balance;

                \App\Models\WalletTransaction::create([
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'type' => 'credit',
                    'description' => 'Admin Credit: ' . $request->reason,
                    'reference_id' => $request->reference_id,
                    'balance_after' => $newBalance
                ]);

            } else {
                if ($user->wallet_balance < $amount) {
                    throw new \Exception("Insufficient balance for debit.");
                }
                $user->decrement('wallet_balance', $amount);
                $newBalance = $user->wallet_balance;

                \App\Models\WalletTransaction::create([
                    'user_id' => $user->id,
                    'amount' => -$amount,
                    'type' => 'debit',
                    'description' => 'Admin Debit: ' . $request->reason,
                    'reference_id' => $request->reference_id,
                    'balance_after' => $newBalance
                ]);
            }

            AdminActivityLogger::log('wallet_adjustment', $user, [
                'type' => $request->type,
                'amount' => $amount,
                'reason' => $request->reason,
                'old_balance' => $oldBalance,
                'new_balance' => $newBalance,
                'idempotency_key' => $request->idempotency_key ?? null
            ]);
        });

        return back()->with('success', "Wallet {$request->type} of ₹{$request->amount} successful.");
    }
}
