<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\DeviceToken;
use App\Services\FCMNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock Firestore
        $mockFirestore = \Mockery::mock();
        $mockFirestore->shouldReceive('database->collection->document->collection->document->set')->andReturn(true);
        $mockFirestore->shouldReceive('database->collection->document->collection->document->delete')->andReturn(true);
        $this->app->instance('firebase.firestore', $mockFirestore);

        // Mock Messaging
        $mockMessaging = \Mockery::mock();
        $this->app->instance('firebase.messaging', $mockMessaging);
    }

    public function test_device_token_registration()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/devices/register', [
            'fcm_token' => 'test_token_123',
            'platform' => 'android',
            'device_id' => 'device_abc'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('device_tokens', [
            'user_id' => $user->id,
            'fcm_token' => 'test_token_123'
        ]);

        // Note: Firestore sync is fire-and-forget, so we don't assert it here unless we mock it.
    }

    public function test_payload_sanitization_removes_pii()
    {
        $service = new FCMNotificationService();
        $payload = [
            'chat_id' => '123',
            'user_phone' => '9999999999', // Should be removed
            'email' => 'test@example.com', // Should be removed
            'safe_key' => 'visible'
        ];

        // Access protected method via reflection if needed, but we can test buildPayload
        $message = $service->buildPayload('test_type', 'Title', 'Body', $payload);
        $data = $message->jsonSerialize()['data'];

        $this->assertArrayNotHasKey('user_phone', $data);
        $this->assertArrayNotHasKey('email', $data);
        $this->assertArrayHasKey('safe_key', $data);
        $this->assertEquals('visible', $data['safe_key']);
    }

    public function test_preview_truncation()
    {
        $service = new FCMNotificationService();
        $longText = str_repeat('A', 100);
        $truncated = $service->truncatePreview($longText, 80);

        $this->assertLessThanOrEqual(83, strlen($truncated)); // 80 + ...
        $this->assertStringEndsWith('...', $truncated);
    }

    public function test_safe_masked_label()
    {
        $service = new FCMNotificationService();

        $user = User::factory()->create(['id' => 50, 'name' => 'John Doe']);
        $user->assignRole('User');

        $label = $service->safeMaskedLabel($user);
        $this->assertEquals('User #U1050', $label);
        $this->assertStringNotContainsString('John', $label); // Privacy Check

        $astro = User::factory()->create(['name' => 'Astro Star']);
        $astro->assignRole('Astrologer');

        $astroLabel = $service->safeMaskedLabel($astro);
        $this->assertEquals('Astrologer Astro Star', $astroLabel);
    }
}
