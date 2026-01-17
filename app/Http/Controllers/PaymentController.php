<?php

namespace App\Http\Controllers;

use App\Models\PaymentOrder;
use App\Models\User;
use App\Services\PhonePeService;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    protected $phonePe;
    protected $wallet;

    public function __construct(PhonePeService $phonePe, WalletService $wallet)
    {
        $this->phonePe = $phonePe;
        $this->wallet = $wallet;
    }

    // 1. Show Recharge Page
    public function showRecharge()
    {
        $user = auth()->user();
        $transactions = $user->walletTransactions()->latest()->limit(5)->get();
        return view('user.wallet.recharge', compact('user', 'transactions'));
    }

    // 2. Initiate Payment
    public function initiateRecharge(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $user = auth()->user();
        $amount = $request->amount;

        // Unique Transaction ID
        $merchantTxnId = 'TXN_' . Str::random(10);

        // Create Pending Order
        $order = PaymentOrder::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'merchant_transaction_id' => $merchantTxnId,
            'status' => 'pending',
            'provider' => 'phonepe',
            'currency' => 'INR',
        ]);

        // Call PhonePe
        $response = $this->phonePe->initiatePayment($merchantTxnId, $amount, $user->id, $user->phone);

        if ($response && isset($response['data']['instrumentResponse']['redirectInfo']['url'])) {
            $order->update(['payment_url' => $response['data']['instrumentResponse']['redirectInfo']['url']]);
            return redirect($response['data']['instrumentResponse']['redirectInfo']['url']);
        }

        return back()->with('error', 'Failed to initiate payment. Please try again.');
    }

    // 3. Handle Webhook (Server-to-Server)
    public function handleWebhook(Request $request)
    {
        $payload = $request->input('response');
        $xVerify = $request->header('X-VERIFY');

        if (!$payload || !$xVerify) {
            return response()->json(['error' => 'Invalid Request'], 400);
        }

        // Verify Signature
        if (!$this->phonePe->verifyCallback($payload, $xVerify)) {
            Log::error('PhonePe Webhook Signature Mismatch');
            return response()->json(['error' => 'Signature Mismatch'], 400);
        }

        $data = json_decode(base64_decode($payload), true);

        if (!$data || !isset($data['data']['merchantTransactionId'])) {
            return response()->json(['error' => 'Invalid Payload'], 400);
        }

        $txnId = $data['data']['merchantTransactionId'];
        $providerRefId = $data['data']['transactionId'] ?? null;
        $status = $data['code'] ?? 'FAILED';

        $order = PaymentOrder::where('merchant_transaction_id', $txnId)->first();

        if (!$order) {
            Log::error('Payment Order Not Found: ' . $txnId);
            return response()->json(['status' => 'Order Not Found'], 404);
        }

        if ($order->status === 'completed') {
            return response()->json(['status' => 'Already Processed']);
        }

        // Handle Success
        if ($status === 'PAYMENT_SUCCESS') {
            // Transactional Update
            try {
                \DB::beginTransaction();

                $order->update([
                    'status' => 'completed',
                    'provider_transaction_id' => $providerRefId,
                    'meta' => $data
                ]);

                // Credit Wallet
                $this->wallet->credit(
                    $order->user,
                    $order->amount,
                    'recharge',
                    $order->id,
                    "Wallet Recharge (Txn: $txnId)",
                    ['provider_ref' => $providerRefId],
                    $txnId // Idempotency
                );

                \DB::commit();
                return response()->json(['status' => 'Success']);
            } catch (\Exception $e) {
                \DB::rollBack();
                Log::error('Wallet Credit Failed: ' . $e->getMessage());
                return response()->json(['error' => 'Internal Error'], 500);
            }
        } else {
            // Failed
            $order->update(['status' => 'failed', 'meta' => $data]);
            return response()->json(['status' => 'Marked Failed']);
        }
    }

    // 4. Handle Redirect (User Return)
    public function handleRedirect(Request $request)
    {
        // PhonePe POSTs to this URL with code, merchantId, transactionId, etc.
        // We generally shouldn't trust this for critical updates, but we can show status.
        // Or we can poll our backend to see if Webhook updated it.

        $code = $request->input('code');

        if ($code === 'PAYMENT_SUCCESS') {
            return redirect()->route('user.wallet')->with('success', 'Payment Successful! Wallet updated.');
        }

        return redirect()->route('user.wallet')->with('error', 'Payment Failed or Pending.');
    }
}
