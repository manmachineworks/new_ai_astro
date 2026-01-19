<?php

namespace Tests\Feature;

use App\Models\Astrologer;
use App\Models\AstrologerPricing;
use App\Models\AstrologerPricingAudit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PricingAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_pricing_update_records_audit()
    {
        $user = User::factory()->create(['role' => 'astrologer']);
        $astrologer = Astrologer::create([
            'user_id' => $user->id,
            'public_id' => 'ASTRO-00002',
            'display_name' => 'Audit Astro',
            'languages' => ['English'],
            'specializations' => ['Money'],
        ]);
        AstrologerPricing::create([
            'astrologer_id' => $astrologer->id,
            'call_per_minute' => 10,
            'chat_price' => 80,
            'ai_chat_price' => 40,
        ]);

        $response = $this->actingAs($user)->put('/astrologer/pricing', [
            'call_per_minute' => 15,
            'chat_price' => 100,
            'ai_chat_price' => 50,
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('astrologer_pricing_audits', [
            'astrologer_id' => $astrologer->id,
            'changed_by_user_id' => $user->id,
        ]);
    }
}
