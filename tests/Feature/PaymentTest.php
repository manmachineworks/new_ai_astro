<?php

namespace Tests\Feature;

use App\Models\PaymentOrder;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\PhonePeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Mockery;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Config::set('services.phonepe.salt_key', 'test-salt-key');
        Config::set('services.phonepe.salt_index', '1');
    }

    public function test_initiate_payment_creates_record_and_returns_url()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        // Mock PhonePeService
        $this->mock(PhonePeService::class, function ($mock) {
            $mock->shouldReceive('initiatePayment')
                ->once()
                ->andReturn([
                    'success' => true,
                    'data' => [
                        'instrumentResponse' => [
                            'redirectInfo' => [
                                'url' => 'https://phonepe.test/pay'
                            ]
                        ]
                    ]
                ]);
        });

        $response = $this->postJson('/api/wallet/recharge', ['amount' => 100]);

        $response->assertOk()
            ->assertJsonStructure(['status', 'redirect_url', 'order_id', 'merchant_transaction_id']);

        $this->assertDatabaseHas('payment_orders', [
            'user_id' => $user->id,
            'amount' => 100.00,
            'status' => 'redirected'
        ]);
    }

    public function test_webhook_success_credits_wallet()
    {
        $user = User::factory()->create(['wallet_balance' => 0]);
        $txnId = 'TXN_' . Str::uuid();

        // Create initial payment record
        $order = PaymentOrder::create([
            'user_id' => $user->id,
            'merchant_transaction_id' => $txnId,
            'amount' => 500.00,
            'status' => 'initiated',
        ]);

        // Payload
        $payload = [
            'code' => 'PAYMENT_SUCCESS',
            'data' => [
                'merchantTransactionId' => $txnId,
                'transactionId' => 'PROVIDER_123',
                'amount' => 50000, // paise
            ]
        ];
        $base64 = base64_encode(json_encode($payload));

        // Calculate Checksum manually matching logic
        $saltKey = 'test-salt-key';
        $saltIndex = '1';
        $checksum = hash('sha256', $base64 . $saltKey) . '###' . $saltIndex;

        // Note: We don't need to mock verifyCallback if we actually provide valid checksum,
        // but since we are injecting logic, let's just rely on the real service logic for verification 
        // OR mock it to be safe. Let's rely on real logic since we set config.

        $response = $this->postJson('/api/webhooks/phonepe', ['response' => $base64], ['X-VERIFY' => $checksum]);

        $response->assertOk()
            ->assertJson(['status' => 'accepted']);

        // Check Payment Status Updated
        $this->assertDatabaseHas('payment_orders', [
            'merchant_transaction_id' => $txnId,
            'status' => 'success',
        ]);

        // Check Wallet Credited
        $this->assertEquals(500.00, $user->fresh()->wallet_balance);

        // Check Transaction Log
        $this->assertDatabaseHas('wallet_transactions', [
            'user_id' => $user->id,
            'amount' => 500.00,
            'type' => 'credit',
            'reference_type' => 'recharge',
            'reference_id' => $order->id,
        ]);
    }

    public function test_webhook_duplicate_idempotency()
    {
        $user = User::factory()->create(['wallet_balance' => 100]);
        $txnId = 'TXN_' . Str::uuid();

        // Create ALREADY SUCCESS payment
        $order = PaymentOrder::create([
            'user_id' => $user->id,
            'merchant_transaction_id' => $txnId,
            'amount' => 500.00,
            'status' => 'success',
        ]);

        WalletTransaction::create([
            'user_id' => $user->id,
            'amount' => 500.00,
            'type' => 'credit',
            'balance_after' => 600.00,
            'currency' => 'INR',
            'source' => 'phonepe',
            'reference_type' => 'recharge',
            'reference_id' => $order->id,
            'description' => 'Wallet Recharge (PhonePe)',
            'idempotency_key' => 'phonepe:' . $txnId,
        ]);

        $payload = [
            'code' => 'PAYMENT_SUCCESS',
            'data' => [
                'merchantTransactionId' => $txnId,
            ]
        ];
        $base64 = base64_encode(json_encode($payload));
        $checksum = hash('sha256', $base64 . 'test-salt-key') . '###' . 1;

        $response = $this->postJson('/api/webhooks/phonepe', ['response' => $base64], ['X-VERIFY' => $checksum]);

        $response->assertOk();

        // Balance should NOT increase again
        $this->assertEquals(100.00, $user->fresh()->wallet_balance);
    }
}
