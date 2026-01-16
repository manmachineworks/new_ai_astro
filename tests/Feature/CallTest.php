<?php

namespace Tests\Feature;

use App\Models\CallSession;
use App\Models\User;
use App\Services\CallerDeskService;
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
        $this->mock(CallerDeskService::class, function ($mock) {
            $mock->shouldReceive('initiateCall')
                ->once()
                ->andReturn(['status' => 'success']);
        });

        $response = $this->postJson('/api/call/initiate', ['astrologer_id' => $astro->id]);

        $response->assertOk()
            ->assertJsonStructure(['call_id']);

        $this->assertDatabaseHas('call_sessions', [
            'user_id' => $user->id,
            'astrologer_user_id' => $astro->id,
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

    public function test_webhook_billing()
    {
        // Setup Users
        $astro = User::factory()->create(['wallet_balance' => 0]);
        $user = User::factory()->create(['wallet_balance' => 100]);

        // Create Session
        $callId = 'CALL_TEST_123';
        $session = CallSession::create([
            'user_id' => $user->id,
            'astrologer_user_id' => $astro->id,
            'status' => 'connecting',
            'rate_per_minute' => 10.00,
            'callerdesk_call_id' => $callId,
        ]);

        // Webhook Payload (3 mins = 60 * 3 = 180 sec) -> 3 * 10 = 30 Cost.
        // Earning = 30 * 0.7 = 21.00
        $response = $this->postJson('/api/webhooks/callerdesk', [
            'reference_id' => $callId,
            'status' => 'completed',
            'duration' => 180
        ]);

        $response->assertOk();

        // Check Session
        $this->assertDatabaseHas('call_sessions', [
            'id' => $session->id,
            'status' => 'completed',
            'duration_seconds' => 180,
            'cost' => 30.00
        ]);

        // Check User Logic (Debit 30)
        $this->assertEquals(70.00, $user->fresh()->wallet_balance);

        // Check Astro Logic (Credit 21)
        $this->assertEquals(21.00, $astro->fresh()->wallet_balance);

        // Log Check
        $this->assertDatabaseHas('wallet_transactions', [
            'user_id' => $user->id,
            'amount' => 30.00,
            'type' => 'debit'
        ]);
        $this->assertDatabaseHas('wallet_transactions', [
            'user_id' => $astro->id,
            'amount' => 21.00,
            'type' => 'credit'
        ]);
    }
}
