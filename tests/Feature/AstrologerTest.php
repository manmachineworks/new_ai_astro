<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AstrologerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'Astrologer', 'guard_name' => 'web']);
    }

    public function test_astrologer_can_update_profile()
    {
        $user = User::factory()->create();
        $user->assignRole('Astrologer');
        Sanctum::actingAs($user, ['*']);

        $payload = [
            'bio' => 'Updated Bio',
            'skills' => ['Vedic', 'Numerology'],
            'languages' => ['English'],
            'call_per_minute' => 20.00,
            'availability_schedule' => ['monday' => ['10:00-12:00']],
        ];

        $response = $this->putJson('/api/astrologer/profile', $payload);

        $response->assertOk();

        $this->assertDatabaseHas('astrologer_profiles', [
            'user_id' => $user->id,
            'bio' => 'Updated Bio',
            'call_per_minute' => 20.00,
        ]);

        $profile = $user->astrologerProfile;
        $this->assertEquals(['Vedic', 'Numerology'], $profile->skills);
        $this->assertEquals(['monday' => ['10:00-12:00']], $profile->availability_schedule);
    }

    public function test_can_search_astrologers_by_skill()
    {
        // User 1: Vedic
        $a1 = User::factory()->create(['name' => 'A1', 'is_active' => true]);
        $a1->assignRole('Astrologer');
        $a1->astrologerProfile()->create([
            'skills' => ['Vedic'],
            'visibility' => true
        ]);

        // User 2: Tarot
        $a2 = User::factory()->create(['name' => 'A2', 'is_active' => true]);
        $a2->assignRole('Astrologer');
        $a2->astrologerProfile()->create([
            'skills' => ['Tarot'],
            'visibility' => true
        ]);

        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        // Search Vedic
        $response = $this->getJson('/api/astrologers?skill=Vedic');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('A1', $response->json('data.0.name'));
    }
}
