<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Services\FirebaseAuthService;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class LoginFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_admin_can_login_and_view_dashboard(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.local',
            'phone' => '+15550000001',
            'password' => 'Secret123!',
        ]);
        $admin->assignRole('Admin');

        $response = $this->post(route('admin.login.submit'), [
            'login' => 'admin@test.local',
            'password' => 'Secret123!',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
        $this->get(route('admin.dashboard'))->assertOk();
    }

    public function test_user_phone_login_creates_user_and_redirects(): void
    {
        $firebase = Mockery::mock(FirebaseAuthService::class);
        $firebase->shouldReceive('verifyIdToken')
            ->once()
            ->andReturn(['uid' => 'uid-user-1', 'phone_number' => '+15550000011']);
        $this->app->instance(FirebaseAuthService::class, $firebase);

        $response = $this->postJson(route('auth.phone.verify'), [
            'firebase_id_token' => 'fake-token',
            'name' => 'Test User',
        ]);

        $response->assertOk();
        $response->assertJson([
            'redirect' => route('user.dashboard'),
        ]);

        $user = User::where('phone', '+15550000011')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('User'));
    }

    public function test_astrologer_phone_login_redirects_to_astrologer_dashboard(): void
    {
        $astrologer = User::create([
            'name' => 'Astrologer User',
            'phone' => '+15550000021',
            'firebase_uid' => 'uid-astro-1',
        ]);
        $astrologer->assignRole('Astrologer');

        $firebase = Mockery::mock(FirebaseAuthService::class);
        $firebase->shouldReceive('verifyIdToken')
            ->once()
            ->andReturn(['uid' => 'uid-astro-1', 'phone_number' => '+15550000021']);
        $this->app->instance(FirebaseAuthService::class, $firebase);

        $response = $this->postJson(route('auth.phone.verify'), [
            'firebase_id_token' => 'fake-token',
        ]);

        $response->assertOk();
        $response->assertJson([
            'redirect' => route('astrologer.dashboard'),
        ]);
    }
}
