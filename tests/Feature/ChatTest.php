<?php

namespace Tests\Feature;

use App\Jobs\ChatBillingJob;
use App\Models\ChatSession;
use App\Models\User;
use App\Services\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ChatTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'Astrologer', 'guard_name' => 'web']);
    }

    public function test_initiate_chat_success()
    {
        $astro = User::factory()->create();
        $astro->assignRole('Astrologer');
        $astro->astrologerProfile()->create([
            'chat_per_session' => 10,
            'is_chat_enabled' => true,
            'visibility' => true
        ]);

        $user = User::factory()->create(['wallet_balance' => 100]);
        Sanctum::actingAs($user, ['*']);

        $response = $this->postJson('/api/chat/initiate', ['astrologer_id' => $astro->id]);

        $response->assertOk()
            ->assertJsonStructure(['status', 'chat_id', 'firebase_chat_id']);

        $this->assertDatabaseHas('chat_sessions', [
            'user_id' => $user->id,
            'astrologer_user_id' => $astro->id,
            'status' => 'active',
            'rate_per_minute' => 10.00
        ]);
    }

    public function test_billing_job_deducts_balance()
    {
        $astro = User::factory()->create(['wallet_balance' => 0]);
        $user = User::factory()->create(['wallet_balance' => 100]);

        // Active Session started 2 mins ago (should be billed)
        $session = ChatSession::create([
            'user_id' => $user->id,
            'astrologer_user_id' => $astro->id,
            'status' => 'active',
            'rate_per_minute' => 10.00,
            'last_billed_at' => now()->subMinutes(2),
        ]);
        // Force update timestamp
        $session->updated_at = now()->subMinutes(2);
        $session->save(['timestamps' => false]);

        $job = new ChatBillingJob();
        $job->handle(new WalletService());

        // Use fresh() to get updated model
        $user->refresh();
        $astro->refresh();
        $session->refresh();

        // Should have deducted 10.00
        $this->assertEquals(90.00, $user->wallet_balance);

        // Astro gets 70% of 10 = 7.00
        $this->assertEquals(7.00, $astro->wallet_balance);

        // Session updated
        $this->assertEquals(10.00, $session->cost);
        $this->assertEquals(1, $session->duration_minutes);
    }

    public function test_billing_job_ends_chat_on_low_balance()
    {
        $astro = User::factory()->create();
        $user = User::factory()->create(['wallet_balance' => 5]); // Less than rate 10

        $session = ChatSession::create([
            'user_id' => $user->id,
            'astrologer_user_id' => $astro->id,
            'status' => 'active',
            'rate_per_minute' => 10.00,
        ]);
        $session->updated_at = now()->subMinutes(2);
        $session->save(['timestamps' => false]);

        $job = new ChatBillingJob();
        $job->handle(new WalletService());

        // Session should be completed (ended)
        $this->assertEquals('completed', $session->fresh()->status);

        // No deduction happened (because debit throws exception before save?) 
        // Logic in Service throws exception, Job catches and ends chat.
        $this->assertEquals(5.00, $user->fresh()->wallet_balance);
    }
}
