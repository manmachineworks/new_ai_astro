<?php

namespace App\Jobs;

use App\Models\AdminActivityLog;
use App\Models\PaymentOrder;
use App\Models\WalletTransaction;
use App\Services\PhonePeService;
use App\Services\WalletService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckPhonePePaymentStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $paymentOrderId;
    protected ?int $adminId;

    public function __construct(string $paymentOrderId, ?int $adminId = null)
    {
        $this->paymentOrderId = $paymentOrderId;
        $this->adminId = $adminId;
    }

    public function handle(PhonePeService $phonePe, WalletService $walletService): void
    {
        $order = PaymentOrder::find($this->paymentOrderId);
        if (!$order) {
            return;
        }

        $response = $phonePe->checkStatus($order->merchant_transaction_id);

        if (!$response) {
            $this->logActivity('finance.payment.recheck_failed', $order, [
                'error' => 'PhonePe status response missing',
            ]);
            return;
        }

        $before = $order->only(['status', 'provider_transaction_id']);
        $code = $response['code'] ?? null;
        $state = $response['data']['state'] ?? $response['data']['status'] ?? null;
        $providerTxnId = $response['data']['transactionId'] ?? null;

        $isSuccess = in_array($code, ['PAYMENT_SUCCESS'], true) || in_array($state, ['COMPLETED', 'SUCCESS'], true);
        $isFailed = in_array($code, ['PAYMENT_ERROR', 'PAYMENT_FAILED'], true) || in_array($state, ['FAILED', 'DECLINED'], true);

        if ($isSuccess) {
            $order->status = 'success';
        } elseif ($isFailed) {
            $order->status = 'failed';
        } else {
            $order->status = $order->status === 'success' ? $order->status : 'pending';
        }

        if ($providerTxnId) {
            $order->provider_transaction_id = $providerTxnId;
        }

        $meta = $order->meta ?? [];
        $meta['status_checks'][] = [
            'checked_at' => now()->toDateTimeString(),
            'response' => $response,
        ];
        $order->meta = $meta;
        $order->save();

        if ($isSuccess) {
            $alreadyCredited = WalletTransaction::where('type', 'credit')
                ->where(function ($q) use ($order) {
                    $q->where(function ($inner) use ($order) {
                        $inner->where('reference_type', 'recharge')
                            ->where('reference_id', $order->id);
                    })->orWhere('idempotency_key', $order->merchant_transaction_id)
                        ->orWhere('idempotency_key', 'phonepe:' . $order->merchant_transaction_id);
                })
                ->exists();

            if (!$alreadyCredited) {
                $walletService->credit(
                    $order->user,
                    $order->amount,
                    'recharge',
                    $order->id,
                    'Wallet Recharge (PhonePe Status Check)',
                    ['provider_ref' => $providerTxnId],
                    'phonepe:' . $order->merchant_transaction_id,
                    'phonepe'
                );
            }
        }

        $this->logActivity('finance.payment.recheck_completed', $order, [
            'before' => $before,
            'after' => $order->only(['status', 'provider_transaction_id']),
            'code' => $code,
            'state' => $state,
        ]);
    }

    protected function logActivity(string $action, PaymentOrder $order, array $metadata = []): void
    {
        try {
            AdminActivityLog::create([
                'causer_id' => $this->adminId,
                'action' => $action,
                'target_type' => $order->getMorphClass(),
                'target_id' => null,
                'metadata' => array_merge([
                    'payment_order_id' => $order->id,
                    'merchant_transaction_id' => $order->merchant_transaction_id,
                ], $metadata),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to log admin activity', ['error' => $e->getMessage()]);
        }
    }
}
