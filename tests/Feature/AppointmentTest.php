<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\AppointmentEvent;
use App\Models\AppointmentSlot;
use App\Models\AstrologerProfile;
use App\Models\NotificationJob;
use App\Models\User;
use App\Services\AppointmentService;
use App\Services\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        Role::findOrCreate('Astrologer', 'web');
    }

    public function test_hold_prevents_double_booking()
    {
        $profile = $this->makeAstrologerProfile();

        $start = now()->addDay()->setTime(10, 0);
        $end = (clone $start)->addMinutes(30);

        $slot = AppointmentSlot::create([
            'astrologer_profile_id' => $profile->id,
            'start_at_utc' => $start,
            'end_at_utc' => $end,
            'duration_minutes' => 30,
            'status' => 'available',
        ]);

        $user1 = User::factory()->create(['wallet_balance' => 500]);
        $user2 = User::factory()->create(['wallet_balance' => 500]);

        Sanctum::actingAs($user1, ['*']);
        $this->postJson('/api/appointments/hold', [
            'astrologer_id' => $profile->id,
            'start_at' => $start->toIso8601String(),
            'end_at' => $end->toIso8601String(),
            'tz' => 'UTC',
        ])->assertOk();

        Sanctum::actingAs($user2, ['*']);
        $this->postJson('/api/appointments/hold', [
            'astrologer_id' => $profile->id,
            'start_at' => $start->toIso8601String(),
            'end_at' => $end->toIso8601String(),
            'tz' => 'UTC',
        ])->assertStatus(409);

        $this->assertDatabaseHas('appointment_slots', [
            'id' => $slot->id,
            'status' => 'held',
            'held_by_user_id' => $user1->id,
        ]);
    }

    public function test_hold_expiry_releases_slot_and_wallet_hold()
    {
        $profile = $this->makeAstrologerProfile();
        $user = User::factory()->create(['wallet_balance' => 300]);

        $start = now()->addDay()->setTime(9, 0);
        $end = (clone $start)->addMinutes(30);

        $slot = AppointmentSlot::create([
            'astrologer_profile_id' => $profile->id,
            'start_at_utc' => $start,
            'end_at_utc' => $end,
            'duration_minutes' => 30,
            'status' => 'held',
            'held_by_user_id' => $user->id,
            'hold_expires_at_utc' => now()->subMinutes(1),
        ]);

        $wallet = app(WalletService::class);
        $hold = $wallet->hold($user, 100, 'appointment_hold', 'appointment_slot', $slot->id, 10);
        $hold->update(['expires_at' => now()->subMinutes(1)]);

        $appointment = Appointment::create([
            'user_id' => $user->id,
            'astrologer_profile_id' => $profile->id,
            'start_at_utc' => $start,
            'end_at_utc' => $end,
            'duration_minutes' => 30,
            'status' => 'requested',
            'pricing_mode' => 'per_minute',
            'price_total' => 100,
            'wallet_hold_id' => $hold->id,
        ]);

        Artisan::call('appointments:reminders');

        $this->assertDatabaseHas('appointment_slots', [
            'id' => $slot->id,
            'status' => 'available',
        ]);
        $this->assertDatabaseHas('wallet_holds', [
            'id' => $hold->id,
            'status' => 'released',
        ]);
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'expired',
        ]);
    }

    public function test_confirm_is_idempotent()
    {
        $profile = $this->makeAstrologerProfile();
        $user = User::factory()->create(['wallet_balance' => 200]);

        $start = now()->addDay()->setTime(11, 0);
        $end = (clone $start)->addMinutes(30);

        $slot = AppointmentSlot::create([
            'astrologer_profile_id' => $profile->id,
            'start_at_utc' => $start,
            'end_at_utc' => $end,
            'duration_minutes' => 30,
            'status' => 'held',
            'held_by_user_id' => $user->id,
            'hold_expires_at_utc' => now()->addMinutes(10),
        ]);

        $wallet = app(WalletService::class);
        $hold = $wallet->hold($user, 100, 'appointment_hold', 'appointment_slot', $slot->id, 10);

        $appointment = Appointment::create([
            'user_id' => $user->id,
            'astrologer_profile_id' => $profile->id,
            'start_at_utc' => $start,
            'end_at_utc' => $end,
            'duration_minutes' => 30,
            'status' => 'requested',
            'pricing_mode' => 'per_minute',
            'price_total' => 100,
            'wallet_hold_id' => $hold->id,
        ]);

        $astroUser = $profile->user;
        $astroUser->assignRole('Astrologer');
        Sanctum::actingAs($astroUser, ['*']);

        $this->postJson("/api/astrologer/appointments/{$appointment->id}/confirm")->assertOk();
        $this->postJson("/api/astrologer/appointments/{$appointment->id}/confirm")->assertOk();

        $this->assertEquals(1, AppointmentEvent::where('appointment_id', $appointment->id)
            ->where('event_type', 'confirmed')
            ->count());
    }

    public function test_cancel_policy_applies()
    {
        config([
            'appointments.cancellation.user_full_refund_hours' => 6,
            'appointments.cancellation.user_partial_refund_percent' => 50,
        ]);

        $profile = $this->makeAstrologerProfile();
        $user = User::factory()->create(['wallet_balance' => 200]);

        $start = now()->addHours(2);
        $end = (clone $start)->addMinutes(30);

        $slot = AppointmentSlot::create([
            'astrologer_profile_id' => $profile->id,
            'start_at_utc' => $start,
            'end_at_utc' => $end,
            'duration_minutes' => 30,
            'status' => 'held',
            'held_by_user_id' => $user->id,
            'hold_expires_at_utc' => now()->addMinutes(10),
        ]);

        $wallet = app(WalletService::class);
        $hold = $wallet->hold($user, 100, 'appointment_hold', 'appointment_slot', $slot->id, 10);

        $appointment = Appointment::create([
            'user_id' => $user->id,
            'astrologer_profile_id' => $profile->id,
            'start_at_utc' => $start,
            'end_at_utc' => $end,
            'duration_minutes' => 30,
            'status' => 'requested',
            'pricing_mode' => 'per_minute',
            'price_total' => 100,
            'wallet_hold_id' => $hold->id,
        ]);

        app(AppointmentService::class)->confirmAppointment($appointment, $profile);

        Sanctum::actingAs($user, ['*']);
        $this->postJson("/api/appointments/{$appointment->id}/cancel")->assertOk();

        $this->assertEquals(150, $user->fresh()->wallet_balance);
    }

    public function test_reminders_scheduled_on_confirm()
    {
        config(['appointments.reminders.lead_minutes' => [1440, 60, 10]]);

        $profile = $this->makeAstrologerProfile();
        $user = User::factory()->create(['wallet_balance' => 200]);

        $start = now()->addDays(2)->setTime(15, 0);
        $end = (clone $start)->addMinutes(30);

        $slot = AppointmentSlot::create([
            'astrologer_profile_id' => $profile->id,
            'start_at_utc' => $start,
            'end_at_utc' => $end,
            'duration_minutes' => 30,
            'status' => 'held',
            'held_by_user_id' => $user->id,
            'hold_expires_at_utc' => now()->addMinutes(10),
        ]);

        $wallet = app(WalletService::class);
        $hold = $wallet->hold($user, 100, 'appointment_hold', 'appointment_slot', $slot->id, 10);

        $appointment = Appointment::create([
            'user_id' => $user->id,
            'astrologer_profile_id' => $profile->id,
            'start_at_utc' => $start,
            'end_at_utc' => $end,
            'duration_minutes' => 30,
            'status' => 'requested',
            'pricing_mode' => 'per_minute',
            'price_total' => 100,
            'wallet_hold_id' => $hold->id,
        ]);

        app(AppointmentService::class)->confirmAppointment($appointment, $profile);

        $this->assertEquals(6, NotificationJob::count());
    }

    protected function makeAstrologerProfile(): AstrologerProfile
    {
        $astroUser = User::factory()->create();
        $astroUser->assignRole('Astrologer');

        return AstrologerProfile::create([
            'user_id' => $astroUser->id,
            'display_name' => 'Astro One',
            'call_per_minute' => 10,
            'is_verified' => true,
            'is_call_enabled' => true,
        ]);
    }
}
