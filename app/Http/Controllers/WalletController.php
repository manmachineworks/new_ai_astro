<?php

namespace App\Http\Controllers;

use App\Models\PaymentOrder;
use App\Models\WebhookEvent;
use App\Services\PhonePeClient;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WalletController extends Controller
{
    protected $phonePe;

    public function __construct(PhonePeClient $phonePe)
    {
        $this->phonePe = $phonePe;
    }

    public function showRecharge()
    {
        return view('wallet.recharge');
    }

    public function balance(Request $request)
    {
        return response()->json([
            'balance' => $request->user()->wallet_balance,
        ]);
    }

    public function transactions(Request $request)
    {
        $transactions = WalletTransaction::where('user_id', $request->user()->id)
            ->latest()
            ->paginate(20);

        return response()->json($transactions);
    }

    // Process Recharge Request
    public function initiateRecharge(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $user = $request->user();
        $amount = $request->amount;
        $merchantTxnId = 'TXN_' . Str::upper(Str::random(12));

        // Create Order
        $order = PaymentOrder::create([
            'user_id' => $user->id,
            'merchant_transaction_id' => $merchantTxnId,
            'amount' => $amount,
            'status' => 'initiated',
        ]);

        // Call PhonePe
        $response = $this->phonePe->createPayment(
            $merchantTxnId,
            $amount,
            (string) $user->id,
            $user->phone ?? '9999999999' // Fallback for dev/sandbox if phone missing
        );

        if ($response && ($response['success'] ?? false)) {
            $instrument = $response['data']['instrumentResponse'] ?? null;
            $payUrl = $instrument['redirectInfo']['url'] ?? null;

            if ($payUrl) {
                $order->update([
                    'status' => 'redirected',
                    'payment_url' => $payUrl,
                    'meta' => ['init_response' => $response]
                ]);

                return redirect($payUrl);
            }
        }

        $order->update(['status' => 'failed', 'meta' => ['failed_response' => $response]]);
        return back()->with('error', 'Failed to initiate payment gateway.');
    }

    public function rechargeReturn(Request $request)
    {
        // PhonePe POSTs back to this URL or GETs it
        // We show a "Processing" page which polls status or just static Wait
        return view('wallet.return');
    }

    // Webhook Handler
    // NOTE: This must be in api.php or excluded from CSRF
    public function handleWebhook(Request $request)
    {
        $rawBody = $request->getContent();
        $headers = $request->headers->all();
        // PhonePe sends checksum in X-VERIFY
        $headerChecksum = $request->header('X-VERIFY');

        // Parse Payload
        $payload = $request->input('response'); // Base64 encoded string

        if (!$payload) {
            return response()->json(['error' => 'Missing Payload'], 400);
        }

        $isValid = $this->phonePe->verifyWebhookSignature($payload, $headerChecksum);

        $decoded = json_decode(base64_decode($payload), true);
        $txnId = $decoded['data']['merchantTransactionId'] ?? null;

        // Log Event
        $event = WebhookEvent::create([
            'provider' => 'phonepe',
            'event_type' => 'PAYMENT_UPDATE',
            'external_id' => $txnId,
            'signature_valid' => $isValid,
            'payload' => $decoded, // Store DECODED or RAW? Storing decoded is easier to read.
            // Actually usually good to store raw input too, but sticking to decoded for utility.
            'headers' => $headers,
        ]);

        if ($isValid) {
            // Dispatch Queue
            \App\Jobs\ProcessPhonePeWebhook::dispatch($event->id);
            return response()->json(['status' => 'accepted']);
        } else {
            return response()->json(['error' => 'Invalid Signature'], 400);
        }
    }
}
