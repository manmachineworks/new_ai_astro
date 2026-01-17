<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\AiChatSession;
use App\Models\AiChatMessage;
use App\Models\PricingSetting;
use App\Services\AstrologyApiClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Mockery\MockInterface;

class AiChatTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['wallet_balance' => 1000]);
        $this->seedPricingDefaults();
    }

    protected function seedPricingDefaults()
    {
        $settings = [
            'ai_chat_enabled' => 1,
            'ai_chat_pricing_mode' => 'per_message',
            'ai_chat_price_per_message' => 10.00,
            'ai_chat_price_per_session' => 150.00,
            'ai_chat_min_wallet_to_start' => 50.00,
            'ai_chat_max_messages_per_day' => 10,
            'ai_chat_disclaimer_text' => 'Safety First.',
        ];

        foreach ($settings as $key => $value) {
            PricingSetting::updateOrCreate(['key' => $key], ['value_json' => $value]);
        }
    }

    /** @test */
    public function it_starts_session_and_charges_per_session_immediately()
    {
        PricingSetting::updateOrCreate(['key' => 'ai_chat_pricing_mode'], ['value_json' => 'per_session']);

        $this->actingAs($this->user)
            ->post(route('user.ai_chat.start'))
            ->assertRedirect();

        $this->user->refresh();
        $this->assertEquals(850, $this->user->wallet_balance); // 1000 - 150
        $this->assertDatabaseHas('ai_chat_sessions', ['user_id' => $this->user->id, 'pricing_mode' => 'per_session']);
    }

    /** @test */
    public function it_blocks_start_if_wallet_insufficient()
    {
        $poorUser = User::factory()->create(['wallet_balance' => 10]);

        $this->actingAs($poorUser)
            ->post(route('user.ai_chat.start'))
            ->assertRedirect(route('wallet.recharge'));
    }

    /** @test */
    public function it_charges_per_message_idempotently()
    {
        $session = AiChatSession::create([
            'user_id' => $this->user->id,
            'pricing_mode' => 'per_message',
            'price_per_message' => 10,
            'status' => 'active'
        ]);

        $clientId = (string) \Illuminate\Support\Str::uuid();

        // Mock AstrologyAPI
        $this->mock(AstrologyApiClient::class, function (MockInterface $mock) {
            $mock->shouldReceive('aiChat')->andReturn(['prediction' => 'Hello', 'request_id' => '123']);
        });

        // First charge
        $this->actingAs($this->user)
            ->post(route('user.ai_chat.send', $session->id), [
                'message' => 'Hi',
                'client_message_id' => $clientId
            ])
            ->assertJson(['success' => true]);

        $this->user->refresh();
        $this->assertEquals(990, $this->user->wallet_balance);

        // Duplicate request (idempotent)
        $this->actingAs($this->user)
            ->post(route('user.ai_chat.send', $session->id), [
                'message' => 'Hi',
                'client_message_id' => $clientId
            ])
            ->assertJson(['success' => true]);

        $this->user->refresh();
        $this->assertEquals(990, $this->user->wallet_balance); // Balance unchanged
    }

    /** @test */
    public function it_refunds_on_astrology_api_failure()
    {
        $session = AiChatSession::create([
            'user_id' => $this->user->id,
            'pricing_mode' => 'per_message',
            'price_per_message' => 10,
            'status' => 'active'
        ]);

        $this->mock(AstrologyApiClient::class, function (MockInterface $mock) {
            $mock->shouldReceive('aiChat')->andThrow(new \Exception("API Down"));
        });

        $this->actingAs($this->user)
            ->post(route('user.ai_chat.send', $session->id), [
                'message' => 'Refund me',
                'client_message_id' => (string) \Illuminate\Support\Str::uuid()
            ])
            ->assertStatus(502);

        $this->user->refresh();
        $this->assertEquals(1000, $this->user->wallet_balance); // Refunded
        $this->assertDatabaseHas('wallet_transactions', ['reference_type' => 'ai_refund']);
    }

    /** @test */
    public function it_enforces_daily_limit()
    {
        $session = AiChatSession::create([
            'user_id' => $this->user->id,
            'pricing_mode' => 'per_session', // No per-message charge for easier test
            'status' => 'active'
        ]);

        // Mock API
        $this->mock(AstrologyApiClient::class, function (MockInterface $mock) {
            $mock->shouldReceive('aiChat')->andReturn(['prediction' => 'Hi']);
        });

        // Send 10 messages (the limit seeded)
        for ($i = 0; $i < 10; $i++) {
            AiChatMessage::create([
                'ai_chat_session_id' => $session->id,
                'role' => 'user',
                'content' => 'msg ' . $i
            ]);
        }

        $this->actingAs($this->user)
            ->post(route('user.ai_chat.send', $session->id), [
                'message' => 'One too many',
                'client_message_id' => (string) \Illuminate\Support\Str::uuid()
            ])
            ->assertStatus(429);
    }
}
