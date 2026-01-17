<?php

namespace Tests\Feature\Security;

use App\Models\User;
use App\Models\AstrologerProfile;
use App\Models\ChatSession;
use App\Models\CallSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class PIIAuditTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $astroUser;
    protected $astroProfile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        Role::findOrCreate('Astrologer', 'web');

        $this->user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'leak_tester@example.com',
            'phone' => '9888877777'
        ]);

        $this->astroUser = User::factory()->create();
        $this->astroUser->assignRole('Astrologer');

        $this->astroProfile = AstrologerProfile::create([
            'user_id' => $this->astroUser->id,
            'is_verified' => true,
            'is_chat_enabled' => true,
            'is_call_enabled' => true,
        ]);
    }

    /** @test */
    public function it_does_not_leak_user_pii_in_astrologer_chat_index()
    {
        ChatSession::create([
            'user_id' => $this->user->id,
            'astrologer_user_id' => $this->astroUser->id,
            'astrologer_profile_id' => $this->astroProfile->id,
            'conversation_id' => 'test_id',
            'status' => 'active'
        ]);

        $this->actingAs($this->astroUser);
        $response = $this->get(route('astrologer.chats'));

        $this->assertNoPIIExposed($response);
    }

    /** @test */
    public function it_does_not_leak_user_pii_in_astrologer_call_history()
    {
        $callData = [
            'user_id' => $this->user->id,
            'status' => 'completed',
            'gross_amount' => 100,
            'user_masked_identifier' => $this->user->email,
        ];

        if (Schema::hasColumn('call_sessions', 'astrologer_profile_id')) {
            $callData['astrologer_profile_id'] = $this->astroProfile->id;
        } else {
            $callData['astrologer_user_id'] = $this->astroUser->id;
        }

        CallSession::create($callData);

        $this->actingAs($this->astroUser);
        $response = $this->get(route('astrologer.calls'));

        $this->assertNoPIIExposed($response);
    }

    protected function assertNoPIIExposed($response)
    {
        $content = $response->getContent();

        // Check for specific email
        $this->assertStringNotContainsString($this->user->email, $content, "User email exposed!");

        // Check for specific phone
        $this->assertStringNotContainsString($this->user->phone, $content, "User phone exposed!");

        // Generic email regex check (optional but thorough)
        // $this->assertDoesNotMatchRegularExpression('/[a-z0-0._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}/i', $content);

        // Generic phone check (e.g. 10 digits)
        // $this->assertDoesNotMatchRegularExpression('/\d{10}/', $content);
    }
}
