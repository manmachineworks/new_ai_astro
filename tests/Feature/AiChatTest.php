<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AiChatTest extends TestCase
{
    use RefreshDatabase;

    public function test_send_ai_message_deducts_balance()
    {
        $user = User::factory()->create(['wallet_balance' => 10]);
        Sanctum::actingAs($user, ['*']);

        $response = $this->postJson('/api/ai/chat', ['message' => 'Hello AI']);

        $response->assertOk()
            ->assertJsonStructure(['user_message', 'ai_message']);

        $this->assertEquals(5.00, $user->fresh()->wallet_balance);

        $this->assertDatabaseHas('ai_chat_sessions', [
            'user_id' => $user->id,
            'total_cost' => 5.00
        ]);

        $this->assertDatabaseHas('ai_chat_messages', [
            'role' => 'user',
            'content' => 'Hello AI'
        ]);

        $this->assertDatabaseHas('ai_chat_messages', [
            'role' => 'assistant'
        ]);
    }

    public function test_send_ai_message_low_balance()
    {
        $user = User::factory()->create(['wallet_balance' => 2]);
        Sanctum::actingAs($user, ['*']);

        $response = $this->postJson('/api/ai/chat', ['message' => 'Hello']);
        $response->assertStatus(402);
    }
}
