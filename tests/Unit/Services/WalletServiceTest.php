<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $walletService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->walletService = new WalletService();
    }

    public function test_credit_increases_balance_and_logs_transaction()
    {
        $user = User::factory()->create(['wallet_balance' => 0]);

        $this->walletService->credit($user, 100.00, 'recharge', 'txn_123', 'Test Recharge');

        $this->assertEquals(100.00, $user->fresh()->wallet_balance);
        $this->assertDatabaseHas('wallet_transactions', [
            'user_id' => $user->id,
            'amount' => 100.00,
            'type' => 'credit',
            'balance_after' => 100.00,
            'reference_id' => 'txn_123',
        ]);
    }

    public function test_debit_decreases_balance_and_logs_transaction()
    {
        $user = User::factory()->create(['wallet_balance' => 100.00]);

        $this->walletService->debit($user, 50.00, 'call', 'call_123', 'Call Charge');

        $this->assertEquals(50.00, $user->fresh()->wallet_balance);
        $this->assertDatabaseHas('wallet_transactions', [
            'user_id' => $user->id,
            'amount' => 50.00,
            'type' => 'debit',
            'balance_after' => 50.00,
        ]);
    }

    public function test_debit_throws_exception_if_insufficient_balance()
    {
        $this->expectException(\Exception::class);

        $user = User::factory()->create(['wallet_balance' => 10.00]);

        $this->walletService->debit($user, 50.00, 'call', 'call_123');
    }
}
