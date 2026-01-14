<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\FirebaseAuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Mockery\MockInterface;
use Tests\TestCase;

class PhoneAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    public function test_verify_endpoint_rejects_missing_token(): void
    {
        $response = $this->postJson(route('auth.phone.verify'), []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['firebase_id_token']);
    }

    public function test_verify_endpoint_rejects_invalid_token(): void
    {
        $this->mockFirebaseService(function (MockInterface $mock) {
            $mock->shouldReceive('verifyIdToken')
                ->once()
                ->andThrow(new \RuntimeException('invalid token'));
        });

        $response = $this->postJson(route('auth.phone.verify'), [
            'firebase_id_token' => 'bad-token',
        ]);

        $response->assertStatus(401);
    }

    public function test_creates_new_user_when_phone_not_exists(): void
    {
        $claims = ['uid' => 'firebase-123', 'phone_number' => '+15555550123'];
        $this->mockFirebaseService(function (MockInterface $mock) use ($claims) {
            $mock->shouldReceive('verifyIdToken')
                ->once()
                ->andReturn($claims);
        });

        $response = $this->postJson(route('auth.phone.verify'), [
            'firebase_id_token' => 'token-123',
            'name' => 'New User',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('users', [
            'phone' => $claims['phone_number'],
            'firebase_uid' => $claims['uid'],
            'name' => 'New User',
        ]);
        $this->assertAuthenticated();
    }

    public function test_logs_in_existing_user_when_phone_exists(): void
    {
        $user = User::factory()->create([
            'phone' => '+15555550123',
            'firebase_uid' => 'firebase-abc',
        ]);

        $claims = ['uid' => 'firebase-abc', 'phone_number' => '+15555550123'];
        $this->mockFirebaseService(function (MockInterface $mock) use ($claims) {
            $mock->shouldReceive('verifyIdToken')
                ->once()
                ->andReturn($claims);
        });

        $response = $this->postJson(route('auth.phone.verify'), [
            'firebase_id_token' => 'token-123',
        ]);

        $response->assertOk();
        $this->assertAuthenticatedAs($user);
    }

    public function test_updates_firebase_uid_when_missing(): void
    {
        $user = User::factory()->create([
            'phone' => '+15555550123',
            'firebase_uid' => null,
        ]);

        $claims = ['uid' => 'firebase-new', 'phone_number' => '+15555550123'];
        $this->mockFirebaseService(function (MockInterface $mock) use ($claims) {
            $mock->shouldReceive('verifyIdToken')
                ->once()
                ->andReturn($claims);
        });

        $response = $this->postJson(route('auth.phone.verify'), [
            'firebase_id_token' => 'token-123',
        ]);

        $response->assertOk();
        $this->assertAuthenticatedAs($user->fresh());
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'firebase_uid' => 'firebase-new',
        ]);
    }

    protected function mockFirebaseService(callable $callback): void
    {
        $this->mock(FirebaseAuthService::class, $callback);
    }
}
