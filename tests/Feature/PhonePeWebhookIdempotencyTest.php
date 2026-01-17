<?php

namespace Tests\Feature;

use App\Jobs\ProcessPhonePeWebhook;
use App\Models\PaymentOrder;
use App\Models\User;
use App\Models\WebhookEvent;
use App\Models\WalletTransaction;
use App\Services\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhonePeWebhookIdempotencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_phonepe_webhook_retry_does_not_double_credit_wallet()
    {
        $user = User::factory()->create(['wallet_balance' => 0]);

        $order = PaymentOrder::create([
            'user_id' => $user->id,
            'merchant_transaction_id' => 'TXN_TEST_001',
            'amount' => 250.00,
            'status' => 'initiated',
        ]);

        $payload = [
            'code' => 'PAYMENT_SUCCESS',
            'data' => [
                'merchantTransactionId' => 'TXN_TEST_001',
                'transactionId' => 'PROVIDER_123',
            ],
        ];

        $event = WebhookEvent::create([
            'provider' => 'phonepe',
            'event_type' => 'PAYMENT_UPDATE',
            'external_id' => 'TXN_TEST_001',
            'signature_valid' => true,
            'payload' => $payload,
            'headers' => [],
            'processing_status' => 'pending',
        ]);

        $job = new ProcessPhonePeWebhook($event->id);
        $job->handle(app(WalletService::class));
        $job->handle(app(WalletService::class));

        $this->assertEquals(250.00, $user->fresh()->wallet_balance);
        $this->assertDatabaseHas('payment_orders', [
            'id' => $order->id,
            'status' => 'success',
        ]);
        $this->assertEquals(1, WalletTransaction::where('reference_type', 'recharge')
            ->where('reference_id', $order->id)
            ->count());
    }
}
