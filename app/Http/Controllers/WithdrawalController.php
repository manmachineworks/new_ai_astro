<?php

namespace App\Http\Controllers;

use App\Models\WithdrawalRequest;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Astrologer: Request Withdrawal
     */
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:500',
            'bank_details' => 'required|string',
        ]);

        $user = $request->user();

        /*
        if (!$user->hasRole('Astrologer')) {
             abort(403);
        }
        */

        // Create Request and Debit Wallet Transactionally
        $withdrawal = DB::transaction(function () use ($user, $request) {
            $withdrawal = WithdrawalRequest::create([
                'user_id' => $user->id,
                'amount' => $request->amount,
                'status' => 'pending',
                'bank_details' => $request->bank_details,
            ]);

            $this->walletService->debit(
                $user,
                $request->amount,
                'withdrawal_request',
                $withdrawal->id,
                "Withdrawal Request #{$withdrawal->id}"
            );

            return $withdrawal;
        });

        return response()->json($withdrawal, 201);
    }

    /**
     * Admin: List Requests
     */
    public function index(Request $request)
    {
        // Add Admin check
        // if (!$request->user()->hasRole('Admin')) abort(403);

        $requests = WithdrawalRequest::with('user')->orderByDesc('created_at')->paginate(20);
        return response()->json($requests);
    }

    /**
     * Admin: Approve/Reject
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_note' => 'nullable|string'
        ]);

        $withdrawal = WithdrawalRequest::findOrFail($id);

        if ($withdrawal->status !== 'pending') {
            return response()->json(['message' => 'Already processed'], 400);
        }

        DB::transaction(function () use ($withdrawal, $request) {
            $withdrawal->update([
                'status' => $request->status,
                'admin_note' => $request->admin_note,
                'processed_at' => now(),
            ]);

            if ($request->status === 'rejected') {
                // Refund to User
                $this->walletService->credit(
                    $withdrawal->user,
                    $withdrawal->amount,
                    'withdrawal_refund',
                    $withdrawal->id,
                    "Refund: Withdrawal Rejected"
                );
            }
            // If approved, money is already debited from user wallet, so "Real World" transfer happens here.
        });

        return response()->json($withdrawal);
    }
}
