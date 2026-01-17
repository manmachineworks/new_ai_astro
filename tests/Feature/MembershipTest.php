<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\MembershipPlan;
use App\Models\UserMembership;
use App\Services\MembershipService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class MembershipTest extends TestCase
{
    // use RefreshDatabase; // Use if DB needs refresh, assuming standard

    protected $user;
    protected $plan;

    protected function setUp(): void
    {
        parent::setUp();
        // Create a test plan
        $this->plan = MembershipPlan::create([
            'name' => 'Gold',
            'slug' => 'gold',
            'price_amount' => 999,
            'duration_days' => 30,
            'benefits_json' => [
                'call_discount_percent' => 20,
                'chat_discount_percent' => 10,
                'ai_free_messages' => 50
            ],
            'is_active' => true
        ]);

        $this->user = User::factory()->create();
    }

    public function test_activate_membership()
    {
        $service = app(MembershipService::class);
        $membership = $service->activate($this->user, $this->plan->id, 'txn_123');

        $this->assertDatabaseHas('user_memberships', [
            'user_id' => $this->user->id,
            'status' => 'active',
            'membership_plan_id' => $this->plan->id
        ]);

        $this->assertEquals('active', $membership->status);
    }

    public function test_discount_calculation()
    {
        // Activate membership
        $service = app(MembershipService::class);
        $service->activate($this->user, $this->plan->id, 'txn_123');

        // Test Call Discount (20% off 100 = 80)
        $result = $service->calculateDiscount($this->user, 'call', 100);
        $this->assertEquals(80, $result['final_amount']);
        $this->assertEquals(20, $result['discount']);

        // Test Chat Discount (10% off 50 = 45)
        $result = $service->calculateDiscount($this->user, 'chat', 50);
        $this->assertEquals(45, $result['final_amount']);
        $this->assertEquals(5, $result['discount']);
    }

    public function test_benefit_consumption()
    {
        // Activate membership
        $service = app(MembershipService::class);
        $service->activate($this->user, $this->plan->id, 'txn_123');

        // Consume 10 messages (Limit 50)
        $success = $service->consumeBenefit($this->user, 'ai_free_messages', 10);
        $this->assertTrue($success);

        $usage = \App\Models\MembershipBenefitUsage::where('user_membership_id', $this->user->activeMembership->id)->first();
        $this->assertEquals(10, $usage->used_count);

        // Consume 41 more -> Should fail (10 + 41 > 50)
        $success = $service->consumeBenefit($this->user, 'ai_free_messages', 41);
        $this->assertFalse($success);

        // Count should remain 10
        $usage->refresh();
        $this->assertEquals(10, $usage->used_count);
    }

    public function test_expiry_command()
    {
        // Create expired membership
        $expiredMembership = UserMembership::create([
            'user_id' => $this->user->id,
            'membership_plan_id' => $this->plan->id,
            'status' => 'active',
            'starts_at_utc' => now()->subDays(40),
            'ends_at_utc' => now()->subDay(), // Expired yesterday
        ]);

        $this->artisan('memberships:expire')
            ->expectsOutput('Checking for expired memberships...')
            ->assertExitCode(0);

        $expiredMembership->refresh();
        $this->assertEquals('expired', $expiredMembership->status);
    }
}
