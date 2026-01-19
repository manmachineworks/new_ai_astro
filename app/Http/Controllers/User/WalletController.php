<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\WalletTransaction;
use App\Services\PhonePeService;
use App\Models\PaymentOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WalletController extends Controller
{
    protected $phonePe;

    public function __construct(PhonePeService $phonePe)
    {
        $this->phonePe = $phonePe;
    }

    public function index()
    {
        $user = auth()->user();
        $walletBalance = $user->wallet_balance ?? 0.00;

        // Transactions (Recent items for overview)
        $transactions = WalletTransaction::where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('user.wallet.index', compact('walletBalance', 'transactions'));
    }

    public function recharge()
    {
        $walletBalance = auth()->user()->wallet_balance ?? 0.00;
        return view('user.wallet.recharge', compact('walletBalance'));
    }

    public function initiate(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $user = auth()->user();
        $amount = $request->amount;
        $merchantTxnId = 'TXN_' . Str::upper(Str::random(12));

        // Create Pending Order
        $order = PaymentOrder::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'merchant_transaction_id' => $merchantTxnId,
            'status' => 'pending',
            'provider' => 'phonepe',
            'currency' => 'INR',
        ]);

        // Initiate via PhonePe Service
        $response = $this->phonePe->initiatePayment($merchantTxnId, $amount, (string) $user->id, $user->phone);

        if ($response && isset($response['data']['instrumentResponse']['redirectInfo']['url'])) {
            $payUrl = $response['data']['instrumentResponse']['redirectInfo']['url'];

            $order->update([
                'payment_url' => $payUrl,
                'status' => 'initiated' // or 'redirected'
            ]);

            return redirect($payUrl);
        }

        // Handle Failure
        $order->update(['status' => 'failed']);
        return redirect()->back()->with('error', 'Failed to initiate payment gateway. Please try again.');
    }

    public function status(Request $request)
    {
        // Typically callback lands here or webhook updates order.
        // User is redirected back here from PhonePe (configure redirect_url in service config to point here).
        // Check local order status.

        $status = $request->query('status', 'pending'); // purely visual if passed directly
        // In reality, we might look up DB status if we have an order ID in session or query param.

        return view('user.wallet.status', compact('status'));
    }

    public function transactions()
    {
        $transactions = WalletTransaction::where('user_id', auth()->id())
            ->latest()
            ->paginate(15);

        return view('user.wallet.transactions', compact('transactions'));
    }
}
