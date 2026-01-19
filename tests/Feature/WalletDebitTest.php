<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletDebitTest extends TestCase
{
    use RefreshDatabase;

    public function test_wallet_debit_prevents_negative_balance()
    {
        $user = User::factory()->create(['wallet_balance' => 10]);
        $this->expectException(\Exception::class);

        $service = app()->make(WalletService::class);
        $service->debit($user, 50, 'call', 'test-001', 'Test charge');
    }
}
