<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WithdrawalRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class WithdrawalTest extends TestCase
{
    use RefreshDatabase;

    public function test_astrologer_can_request_withdrawal()
    {
        $user = User::factory()->create(['wallet_balance' => 1000]);
        Sanctum::actingAs($user, ['*']);

        $response = $this->postJson('/api/withdrawals', [
            'amount' => 500,
            'bank_details' => 'Acct: 123456',
        ]);

        $response->assertCreated();

        // Check Balance Debited immediately
        $this->assertEquals(500.00, $user->fresh()->wallet_balance);

        $this->assertDatabaseHas('withdrawal_requests', [
            'user_id' => $user->id,
            'amount' => 500.00,
            'status' => 'pending'
        ]);

        $this->assertDatabaseHas('wallet_transactions', [
            'user_id' => $user->id,
            'amount' => 500.00,
            'type' => 'debit'
        ]);
    }

    public function test_admin_reject_refunds_balance()
    {
        $user = User::factory()->create(['wallet_balance' => 500]);
        $request = WithdrawalRequest::create([
            'user_id' => $user->id,
            'amount' => 500,
            'status' => 'pending',
            'bank_details' => 'ABC',
        ]);
        // Simulate initial debit
        $user->wallet_balance = 0;
        $user->save();

        Sanctum::actingAs($user, ['*']); // Acting as admin ideally, but controller checks auth only for now

        $response = $this->putJson("/api/withdrawals/{$request->id}", [
            'status' => 'rejected',
            'admin_note' => 'Invalid Bank'
        ]);

        $response->assertOk();

        // Check Refund
        $this->assertEquals(500.00, $user->fresh()->wallet_balance);
        $this->assertEquals('rejected', $request->fresh()->status);
    }
}
