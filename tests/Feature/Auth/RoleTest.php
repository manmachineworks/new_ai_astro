<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase; // Use with caution on local DB, maybe just manual cleanup or use distinct DB

    public function test_user_can_be_assigned_role()
    {
        $user = User::factory()->create([
            'phone' => '1234567890',
            'wallet_balance' => 0,
        ]);

        $role = Role::firstOrCreate(['name' => 'User', 'guard_name' => 'web']);

        $user->assignRole('User');

        $this->assertTrue($user->hasRole('User'));
    }

    public function test_astrologer_has_profile()
    {
        $user = User::factory()->create([
            'phone' => '9876543210',
            'wallet_balance' => 0,
        ]);

        \Spatie\Permission\Models\Role::create(['name' => 'Astrologer', 'guard_name' => 'web']);
        $user->assignRole('Astrologer');

        $profile = $user->astrologerProfile()->create([
            'bio' => 'Test Bio',
            'call_per_minute' => 10.00,
            'chat_per_session' => 50.00,
        ]);

        $this->assertNotNull($user->astrologerProfile);
        $this->assertEquals('Test Bio', $user->astrologerProfile->bio);
        $this->assertEquals(10.00, $user->astrologerProfile->call_per_minute);
    }
}
