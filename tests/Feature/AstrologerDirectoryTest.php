<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\AstrologerProfile;
use App\Models\PricingSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AstrologerDirectoryTest extends TestCase
{
    use RefreshDatabase;

    protected $astrologer;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed Roles directly or mock if seeder missing, but RolePermissionSeeder usually exists from M1.
        // If it fails, I'll fix it. For now assume RolePermissionSeeder exists.
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);

        // Inline Pricing Settings
        \App\Models\PricingSetting::updateOrCreate(['key' => 'min_wallet_to_start_call'], ['value_json' => 50]);
        \App\Models\PricingSetting::updateOrCreate(['key' => 'min_wallet_to_start_chat'], ['value_json' => 30]);

        // Create Verified Astrologer
        $user = User::factory()->create();
        $user->assignRole('Astrologer');

        $this->astrologer = AstrologerProfile::create([
            'user_id' => $user->id,
            'display_name' => 'Star Master',
            'is_verified' => true,
            'show_on_front' => true,
            'is_enabled' => true,
            'start_call_enabled' => true, // Legacy check? Logic relies on is_call_enabled
            'is_call_enabled' => true,
            'is_chat_enabled' => true,
            'call_per_minute' => 10,
            'chat_per_session' => 10,
        ]);

        // Create Regular User
        $this->user = User::factory()->create();
    }

    public function test_directory_lists_verified_visible_astrologers()
    {
        $response = $this->get('/astrologers');
        $response->assertStatus(200);
        $response->assertSee('Star Master');
    }

    public function test_directory_hides_unverified_astrologers()
    {
        $this->astrologer->update(['is_verified' => false]);

        $response = $this->get('/astrologers');
        $response->assertStatus(200);
        $response->assertDontSee('Star Master');
    }

    public function test_directory_hides_hidden_astrologers()
    {
        $this->astrologer->update(['show_on_front' => false]);

        $response = $this->get('/astrologers');
        $response->assertStatus(200);
        $response->assertDontSee('Star Master');
    }

    public function test_gate_returns_error_if_not_logged_in()
    {
        $response = $this->postJson("/api/astrologers/{$this->astrologer->id}/gate/call");
        $response->assertStatus(401);
    }

    public function test_gate_returns_error_if_low_balance()
    {
        $this->user->update(['wallet_balance' => 0]); // Zero balance

        $response = $this->actingAs($this->user)->postJson("/api/astrologers/{$this->astrologer->id}/gate/call");

        $response->assertStatus(402);
        $response->assertJson(['error' => true]);
    }

    public function test_gate_returns_success_if_balance_sufficient()
    {
        // Set Min Balance to 50
        // PricingSetting::set('min_wallet_to_start_call', 50); // Assuming helper exists, or raw DB
        \App\Models\PricingSetting::updateOrCreate(['key' => 'min_wallet_to_start_call'], ['value' => 50, 'group' => 'global', 'type' => 'number']);

        $this->user->update(['wallet_balance' => 100]); // Sufficient

        $response = $this->actingAs($this->user)->postJson("/api/astrologers/{$this->astrologer->id}/gate/call");

        $response->assertStatus(200);
        $response->assertJsonFragment(['success' => true]);
        $response->assertJsonStructure(['token', 'action']);
    }

    public function test_gate_checks_astrologer_service_enabled()
    {
        $this->astrologer->update(['is_call_enabled' => false]);
        $this->user->update(['wallet_balance' => 100]);

        $response = $this->actingAs($this->user)->postJson("/api/astrologers/{$this->astrologer->id}/gate/call");

        $response->assertStatus(400);
        $response->assertJson(['message' => 'Calls disabled for this astrologer']);
    }
}
