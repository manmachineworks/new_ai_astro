<?php

namespace App\Http\Controllers;

use App\Models\PaymentOrder;
use App\Models\User;
use App\Models\WebhookEvent;
use App\Jobs\ProcessPhonePeWebhook;
use App\Services\PhonePeService;
use App\Services\WebhookPayloadMasker;
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

    public function initiate(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $user = $request->user();
        $amount = $request->amount;
        $merchantTxnId = 'TXN_' . Str::upper(Str::random(12));

        $order = PaymentOrder::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'merchant_transaction_id' => $merchantTxnId,
            'status' => 'initiated',
            'provider' => 'phonepe',
            'currency' => 'INR',
        ]);

        $response = $this->phonePe->initiatePayment($merchantTxnId, $amount, (string) $user->id, $user->phone);

        if ($response && isset($response['data']['instrumentResponse']['redirectInfo']['url'])) {
            $payUrl = $response['data']['instrumentResponse']['redirectInfo']['url'];

            $order->update([
                'status' => 'redirected',
                'payment_url' => $payUrl,
                'meta' => ['init_response' => $response],
            ]);

            return response()->json([
                'status' => 'success',
                'redirect_url' => $payUrl,
                'order_id' => $order->id,
                'merchant_transaction_id' => $merchantTxnId,
            ]);
        }

        $order->update(['status' => 'failed', 'meta' => ['failed_response' => $response]]);
        return response()->json(['status' => 'failed', 'message' => 'Failed to initiate payment.'], 502);
    }

    // 3. Handle Webhook (Server-to-Server)
    public function handleWebhook(Request $request)
    {
        $payload = $request->input('response');
        $xVerify = $request->header('X-VERIFY');

        if (!$payload || !$xVerify) {
            return response()->json(['error' => 'Invalid Request'], 400);
        }

        $isValid = $this->phonePe->verifyCallback($payload, $xVerify);
        $decoded = json_decode(base64_decode($payload), true);
        $txnId = $decoded['data']['merchantTransactionId'] ?? null;

        $headers = WebhookPayloadMasker::mask($request->headers->all());
        $eventPayload = WebhookPayloadMasker::mask([
            'raw' => $payload,
            'decoded' => $decoded,
        ]);

        $alreadyProcessed = false;
        if ($txnId) {
            $alreadyProcessed = WebhookEvent::query()
                ->where('provider', 'phonepe')
                ->where('external_id', $txnId)
                ->where('processing_status', 'processed')
                ->exists();
        }

        $event = WebhookEvent::create([
            'provider' => 'phonepe',
            'event_type' => $decoded['code'] ?? 'PAYMENT_UPDATE',
            'external_id' => $txnId,
            'signature_valid' => $isValid,
            'payload' => $eventPayload,
            'headers' => $headers,
            'processing_status' => $isValid && !$alreadyProcessed ? 'pending' : ($alreadyProcessed ? 'duplicate' : 'failed'),
            'error_message' => $isValid ? null : 'Invalid signature',
        ]);

        if (!$isValid) {
            return response()->json(['error' => 'Signature Mismatch'], 400);
        }

        if ($alreadyProcessed) {
            return response()->json(['status' => 'duplicate', 'event_id' => $event->id]);
        }

        ProcessPhonePeWebhook::dispatch($event->id);

        return response()->json(['status' => 'accepted', 'event_id' => $event->id]);
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
