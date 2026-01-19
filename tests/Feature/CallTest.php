<?php

namespace Tests\Feature;

use App\Models\CallSession;
use App\Models\User;
use App\Services\CallerDeskClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CallTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'Astrologer', 'guard_name' => 'web']);
    }

    public function test_initiate_call_success()
    {
        // Astrologer
        $astro = User::factory()->create(['phone' => '9999999999']);
        $astro->assignRole('Astrologer');
        $astro->astrologerProfile()->create([
            'call_per_minute' => 10,
            'is_call_enabled' => true,
            'visibility' => true
        ]);

        // User with balance (50 required for 5 mins)
        $user = User::factory()->create(['wallet_balance' => 100]);
        Sanctum::actingAs($user, ['*']);

        // Mock Service
        $this->mock(CallerDeskClient::class, function ($mock) {
            $mock->shouldReceive('initiateMaskedCall')
                ->once()
                ->andReturn(['status' => 'success', 'provider_call_id' => 'CD_123']);
        });

        $response = $this->postJson('/api/call/initiate', ['astrologer_id' => $astro->id]);

        $response->assertOk()
            ->assertJsonStructure(['call_id', 'provider_call_id']);

        $this->assertDatabaseHas('call_sessions', [
            'user_id' => $user->id,
            'astrologer_profile_id' => $astro->astrologerProfile->id,
            'status' => 'connecting',
            'rate_per_minute' => 10.00
        ]);
    }

    public function test_initiate_call_insufficient_funds()
    {
        $astro = User::factory()->create();
        $astro->assignRole('Astrologer');
        $astro->astrologerProfile()->create([
            'call_per_minute' => 10,
            'is_call_enabled' => true,
            'visibility' => true
        ]);

        $user = User::factory()->create(['wallet_balance' => 49]); // Need 50
        Sanctum::actingAs($user, ['*']);

        $response = $this->postJson('/api/call/initiate', ['astrologer_id' => $astro->id]);

        $response->assertStatus(402); // Payment Required
    }

    public function test_initiate_call_missing_pricing_returns_validation_error()
    {
        $astro = User::factory()->create();
        $astro->assignRole('Astrologer');
        $astro->astrologerProfile()->create([
            'call_per_minute' => 0,
            'is_call_enabled' => true,
            'visibility' => true
        ]);

        $user = User::factory()->create(['wallet_balance' => 500]);
        Sanctum::actingAs($user, ['*']);

        $this->mock(CallerDeskService::class, function ($mock) {
            $mock->shouldReceive('initiateCall')->never();
        });

        $response = $this->postJson('/api/call/initiate', ['astrologer_id' => $astro->id]);

        $response->assertStatus(422)
            ->assertJson(['message' => 'Call pricing is not configured. Please try again later.']);

        $this->assertDatabaseMissing('call_sessions', [
            'user_id' => $user->id,
        ]);
    }

    public function test_webhook_billing()
    {
        // Setup Users
        $astro = User::factory()->create(['wallet_balance' => 0]);
        $user = User::factory()->create(['wallet_balance' => 100]);

        // Create Session
        $callId = 'CALL_TEST_123';
        $session = CallSession::create([
            'user_id' => $user->id,
            'astrologer_profile_id' => $astro->astrologerProfile->id,
            'status' => 'connecting',
            'rate_per_minute' => 10.00,
            'provider_call_id' => $callId,
        ]);

        // Webhook Payload (3 mins = 60 * 3 = 180 sec) -> 3 * 10 = 30 Cost.
        // Earning = 30 * 0.7 = 21.00
        $response = $this->postJson('/api/webhooks/callerdesk', [
            'call_id' => $callId,
            'status' => 'completed',
            'duration' => 180
        ]);

        $response->assertOk();

        // Check Session
        $this->assertDatabaseHas('call_sessions', [
            'id' => $session->id,
            'status' => 'completed',
            'duration_seconds' => 180,
            'gross_amount' => 30.00
        ]);

        // Check User Logic (Debit 30)
        $this->assertEquals(70.00, $user->fresh()->wallet_balance);

        // Log Check
        $this->assertDatabaseHas('wallet_transactions', [
            'user_id' => $user->id,
            'amount' => 30.00,
            'type' => 'debit'
        ]);
        $this->assertDatabaseHas('astrologer_earnings_ledger', [
            'astrologer_profile_id' => $astro->astrologerProfile->id,
            'amount' => 24.00,
            'status' => 'available'
        ]);
    }
}
