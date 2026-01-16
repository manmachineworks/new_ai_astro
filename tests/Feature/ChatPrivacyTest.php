<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\AstrologerProfile;
use App\Models\ChatSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use App\Services\FirebaseService;

class ChatPrivacyTest extends TestCase
{
    use RefreshDatabase;

    public function test_astrologer_endpoints_do_not_expose_user_pii()
    {
        // Mock Firebase
        $this->mock(FirebaseService::class);

        // Reset cached roles and permissions
        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // Setup Roles
        $role = Role::create(['name' => 'Astrologer', 'guard_name' => 'web']);

        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'password' => bcrypt('password')
        ]);

        $astroUser = User::create([
            'name' => 'Astro User',
            'email' => 'astro@example.com',
            'phone' => '0987654321',
            'password' => bcrypt('password')
        ]);
        $astroUser->assignRole('Astrologer');

        $astro = AstrologerProfile::create([
            'user_id' => $astroUser->id,
            'display_name' => 'Master Astro',
            'verification_status' => 'verified',
            'visibility' => true,
            'is_enabled' => true,
            'is_chat_enabled' => true
        ]);

        $session = ChatSession::create([
            'user_id' => $user->id,
            'astrologer_user_id' => $astroUser->id, // Legacy field support
            'astrologer_profile_id' => $astro->id,
            'conversation_id' => 'test_id',
            'status' => 'active'
        ]);

        $this->actingAs($astroUser, 'web');
        $this->withoutMiddleware([\Spatie\Permission\Middleware\RoleMiddleware::class]);

        $response = $this->get(route('astrologer.chats'));

        $response->assertStatus(200);
        $response->assertDontSee('john@example.com');
        $response->assertDontSee('1234567890');
        // Masked checking
        $response->assertSee('User #' . substr($user->id, -4));
    }
}
