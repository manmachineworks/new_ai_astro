<?php

namespace App\Jobs;

use App\Models\PaymentOrder;
use App\Models\WalletTransaction;
use App\Models\WebhookEvent;
use App\Services\WalletService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessPhonePeWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $webhookEventId;

    public function __construct(string $webhookEventId)
    {
        $this->webhookEventId = $webhookEventId;
    }

    public function handle(WalletService $walletService): void
    {
        $event = WebhookEvent::find($this->webhookEventId);

        if (!$event) {
            return;
        }

        if (!$event->signature_valid) {
            $event->update([
                'processing_status' => 'failed',
                'error_message' => 'Invalid Callback Signature'
            ]);
            return;
        }

        try {
            // $event->payload is already cast to Array by model casts
            $payload = $event->payload;

            if (!is_array($payload)) {
                // Should be array due to casts, but safeguard
                throw new \Exception('Invalid payload format');
            }

            // Check Payload Structure
            if (!isset($payload['data']['merchantTransactionId'])) {
                throw new \Exception('Invalid payload structure: Missing merchantTransactionId');
            }

            $txnId = $payload['data']['merchantTransactionId'];
            $providerTxnId = $payload['data']['transactionId'] ?? null;
            $code = $payload['code'] ?? 'FAILED';

            // Find Order
            $order = PaymentOrder::where('merchant_transaction_id', $txnId)->first();

            if (!$order) {
                throw new \Exception("Payment Order not found for Txn: $txnId");
            }

            // Update Order
            if ($code === 'PAYMENT_SUCCESS') {
                $order->update([
                    'status' => 'success',
                    'provider_transaction_id' => $providerTxnId,
                    'meta' => array_merge($order->meta ?? [], ['webhook' => $payload])
                ]);

                // Credit Wallet (Idempotent)
                if (!$this->hasRechargeCredit($order, $txnId)) {
                    $walletService->credit(
                        $order->user,
                        $order->amount,
                        'recharge',
                        $order->id,
                        "Wallet Recharge (PhonePe)",
                        ['provider_ref' => $providerTxnId],
                        "phonepe:{$txnId}", // Idempotency Key
                        'phonepe'
                    );
                }

            } else {
                $order->update([
                    'status' => 'failed',
                    'meta' => array_merge($order->meta ?? [], ['webhook_failure' => $payload])
                ]);
            }

            $event->update([
                'processing_status' => 'processed',
                'processed_at' => now()
            ]);

        } catch (\Exception $e) {
            Log::error('PhonePe Webhook Processing Failed', ['error' => $e->getMessage()]);

            $event->update([
                'processing_status' => 'failed',
                'error_message' => $e->getMessage()
            ]);

            // Don't release job back to queue unless it's a transient error
            // For logic errors like 'Payment Order not found', we stop.
        }
    }

    protected function hasRechargeCredit(PaymentOrder $order, string $txnId): bool
    {
        return WalletTransaction::query()
            ->where('type', 'credit')
            ->where(function ($query) use ($order, $txnId) {
                $query->where(function ($inner) use ($order) {
                    $inner->where('reference_type', 'recharge')
                        ->where('reference_id', $order->id);
                })->orWhere('idempotency_key', $txnId)
                    ->orWhere('idempotency_key', 'phonepe:' . $txnId);
            })
            ->exists();
    }
}
