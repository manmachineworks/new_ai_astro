<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\CallSession;
use App\Models\ChatSession;
use App\Models\AiChatSession;
use App\Models\DailyMetric;
use App\Models\PricingSetting;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class AdminReportingTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('permission:cache-reset');
        Role::findOrCreate('Admin', 'web');
        Role::findOrCreate('Astrologer', 'web');

        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');
    }

    /** @test */
    public function it_computes_daily_metrics_correctly()
    {
        // 1. Seed some data for today (IST)
        $todayIst = Carbon::now('Asia/Kolkata');
        $user = User::factory()->create();

        // Call Session
        $astroUser = User::factory()->create();
        $astroProfile = \App\Models\AstrologerProfile::create([
            'user_id' => $astroUser->id,
            'is_verified' => true,
            'is_visible' => true,
        ]);

        CallSession::create([
            'user_id' => $user->id,
            'astrologer_profile_id' => $astroProfile->id,
            'gross_amount' => 100,
            'platform_commission_amount' => 20,
            'status' => 'completed',
        ]);

        // 2. Run command
        $this->artisan('metrics:daily', ['date' => $todayIst->toDateString()])->assertExitCode(0);

        // 3. Verify metric record
        $metric = DailyMetric::whereDate('date_ist', $todayIst->toDateString())->first();
        $this->assertNotNull($metric);
        $this->assertEquals(100.00, (float) $metric->call_gross);
        $this->assertEquals(20.00, (float) $metric->call_commission);
    }

    /** @test */
    public function it_snapshots_commission_in_chat_sessions()
    {
        PricingSetting::updateOrCreate(['key' => 'platform_commission_percent'], ['value_json' => 15]);

        $user = User::factory()->create();

        // Simulating the controller logic for snapshotting
        $astroUser = User::factory()->create();
        $astroProfile = \App\Models\AstrologerProfile::create([
            'user_id' => $astroUser->id,
            'is_verified' => true,
            'is_visible' => true,
        ]);

        $session = ChatSession::create([
            'user_id' => $user->id,
            'astrologer_user_id' => $astroUser->id,
            'astrologer_profile_id' => $astroProfile->id,
            'conversation_id' => 'test_id',
            'commission_percent_snapshot' => 15,
            'status' => 'active'
        ]);

        $this->assertEquals(15.00, (float) $session->commission_percent_snapshot);

        PricingSetting::updateOrCreate(['key' => 'platform_commission_percent'], ['value_json' => 25]);
        $this->assertEquals(15.00, (float) $session->fresh()->commission_percent_snapshot);
    }

    /** @test */
    public function it_restricts_reporting_dashboard_to_admins()
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->get(route('admin.reports.dashboard'))
            ->assertStatus(403);

        $this->actingAs($this->admin)
            ->get(route('admin.reports.dashboard'))
            ->assertStatus(200);
    }

    /** @test */
    public function it_exports_csv_successfully()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.reports.revenue', ['export' => 1]));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
        $response->assertHeader('Content-Disposition', 'attachment; filename="revenue_summary.csv"');
    }
}
