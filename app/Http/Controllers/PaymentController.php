<?php

namespace App\Http\Controllers;

use App\Models\PhonepePayment;
use App\Services\PhonePeService;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    protected $phonePeService;
    protected $walletService;

    public function __construct(PhonePeService $phonePeService, WalletService $walletService)
    {
        $this->phonePeService = $phonePeService;
        $this->walletService = $walletService;
    }

    public function initiate(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $user = $request->user();
        $amount = $request->amount;
        $merchantTxnId = 'TXN_' . Str::uuid()->toString();

        // Create Payment Record
        $payment = PhonepePayment::create([
            'user_id' => $user->id,
            'merchant_txn_id' => $merchantTxnId,
            'amount' => $amount,
            'status' => 'initiated',
        ]);

        // Call PhonePe API
        $response = $this->phonePeService->initiatePayment($merchantTxnId, $amount, (string) $user->id, $user->phone);

        if ($response && isset($response['data']['instrumentResponse']['redirectInfo']['url'])) {
            $payment->update([
                'request_payload' => $response,
                'status' => 'pending'
            ]);

            return response()->json([
                'status' => 'success',
                'redirect_url' => $response['data']['instrumentResponse']['redirectInfo']['url'],
                'txn_id' => $merchantTxnId
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'Payment initiation failed'], 500);
    }

    public function initiateWeb(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $user = $request->user();
        $amount = $request->amount;
        $merchantTxnId = 'TXN_' . Str::uuid()->toString();

        // Create Payment Record
        $payment = PhonepePayment::create([
            'user_id' => $user->id,
            'merchant_txn_id' => $merchantTxnId,
            'amount' => $amount,
            'status' => 'initiated',
        ]);

        // Call PhonePe API
        $response = $this->phonePeService->initiatePayment($merchantTxnId, $amount, (string) $user->id, $user->phone);

        if ($response && isset($response['data']['instrumentResponse']['redirectInfo']['url'])) {
            $payment->update([
                'request_payload' => $response,
                'status' => 'pending'
            ]);

            return redirect($response['data']['instrumentResponse']['redirectInfo']['url']);
        }

        return back()->with('error', 'Payment initiation failed');
    }

    public function callback(Request $request)
    {
        $response = $request->input('response');
        $xVerify = $request->header('X-VERIFY');

        if (!$response || !$xVerify) {
            return response()->json(['status' => 'error', 'message' => 'Invalid request'], 400);
        }

        if (!$this->phonePeService->verifyCallback($response, $xVerify)) {
            Log::warning('PhonePe Webhook Signature Verification Failed');
            return response()->json(['status' => 'error', 'message' => 'Signature verification failed'], 403);
        }

        $decoded = json_decode(base64_decode($response), true);

        if (!$decoded || !isset($decoded['data']['merchantTransactionId'])) {
            return response()->json(['status' => 'error', 'message' => 'Invalid payload'], 400);
        }

        $txnId = $decoded['data']['merchantTransactionId'];
        $code = $decoded['code'];

        $payment = PhonepePayment::where('merchant_txn_id', $txnId)->first();

        if (!$payment) {
            Log::error('PhonePe Webhook: Payment not found', ['txn_id' => $txnId]);
            return response()->json(['status' => 'error', 'message' => 'Payment not found'], 404);
        }

        // Idempotency: If already processed, return success
        if ($payment->status === 'success' || $payment->status === 'failed') {
            return response()->json(['status' => 'success', 'message' => 'Already processed']);
        }

        if ($code === 'PAYMENT_SUCCESS') {
            // Transactional Update
            DB::transaction(function () use ($payment, $decoded) {
                $payment->update([
                    'status' => 'success',
                    'phonepe_txn_id' => $decoded['data']['transactionId'] ?? null,
                    'response_payload' => $decoded
                ]);

                if ($payment->type === 'membership') {
                    // Activate Membership
                    $planId = $payment->meta_json['plan_id'] ?? null;
                    if ($planId) {
                        try {
                            app(\App\Services\MembershipService::class)->activate(
                                $payment->user,
                                $planId,
                                $payment->merchant_txn_id
                            );
                        } catch (\Exception $e) {
                            Log::error('Membership Activation Failed', ['error' => $e->getMessage()]);
                        }
                    }
                } else {
                    // Default: Credit Wallet (Recharge)
                    $this->walletService->credit(
                        $payment->user,
                        $payment->amount,
                        'recharge',
                        $payment->merchant_txn_id,
                        'Wallet Recharge via PhonePe',
                        ['provider_ref' => $decoded['data']['transactionId'] ?? null]
                    );
                }
            });
        } else {
            $payment->update([
                'status' => 'failed',
                'response_payload' => $decoded
            ]);
        }

        return response()->json(['status' => 'success']);
    }
}
