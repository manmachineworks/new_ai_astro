<?php

namespace Tests\Feature;

use App\Models\Astrologer;
use App\Models\CallLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AstrologerAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_astrologer_cannot_view_other_call_logs()
    {
        $owner = User::factory()->create(['role' => 'astrologer']);
        $other = User::factory()->create(['role' => 'astrologer']);

        $ownerProfile = Astrologer::create([
            'user_id' => $owner->id,
            'public_id' => 'ASTRO-00001',
            'display_name' => 'Owner',
            'languages' => ['English'],
            'specializations' => ['Love'],
        ]);

        $call = CallLog::create([
            'astrologer_id' => $ownerProfile->id,
            'user_id' => $other->id,
            'user_public_id' => $other->publicId(),
            'status' => 'ended',
            'duration_seconds' => 300,
            'amount_charged' => 100,
            'rate_per_minute' => 20,
        ]);

        $response = $this->actingAs($other)->get("/astrologer/calls/{$call->id}");
        $response->assertStatus(403);
    }
}
