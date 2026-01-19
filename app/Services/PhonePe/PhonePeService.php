<?php

namespace App\Services\PhonePe;

use App\Models\User;
use App\Services\WalletService;
use Illuminate\Support\Str;

class PhonePeService
{
    protected string $callbackUrl;
    protected string $webhookSecret;

    public function __construct()
    {
        $this->callbackUrl = config('phonepe.callback_url');
        $this->webhookSecret = config('phonepe.webhook_secret', '');
    }

    public function verifyWebhook(array $headers, string $payload): bool
    {
        $signature = $headers['x-verify'] ?? $headers['x-verify-signature'] ?? '';

        if (empty($signature) || empty($this->webhookSecret)) {
            return false;
        }

        $calculated = hash('sha256', $payload . $this->webhookSecret);
        return hash_equals($calculated, $signature);
    }

    public function parseWebhook(array $payload): array
    {
        return [
            'event_id' => $payload['merchantTransactionId'] ?? $payload['transactionId'] ?? $payload['paymentId'] ?? Str::uuid(),
            'status' => $payload['status'] ?? 'FAILED',
            'amount' => isset($payload['amount']) ? floatval($payload['amount']) / 100 : 0,
            'reference_id' => $payload['merchantTransactionId'] ?? $payload['referenceId'] ?? null,
            'user_id' => $payload['merchantUserId'] ?? null,
            'raw' => $payload,
        ];
    }

    public function creditWalletIfPaid(User $user, array $event, WalletService $walletService)
    {
        if (($event['status'] ?? '') !== 'SUCCESS') {
            return null;
        }

        $amount = $event['amount'] ?? 0;

        return $walletService->credit(
            $user,
            $amount,
            'phonepe',
            $event['reference_id'] ?? $event['event_id'] ?? null,
            'PhonePe Recharge',
            ['source_payload' => $event['raw'] ?? []],
            $event['event_id'] ?? null,
            'phonepe'
        );
    }
}
