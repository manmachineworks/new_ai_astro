<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\AppointmentSlot;
use App\Services\AppointmentService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

class AstrologerAppointmentController extends Controller
{
    public function index(Request $request)
    {
        $profile = $request->user()->astrologerProfile;
        if (!$profile) {
            abort(403, 'Astrologer profile not found.');
        }

        $tz = $request->input('tz', config('appointments.default_timezone', 'Asia/Kolkata'));

        $pending = Appointment::with('user')
            ->where('astrologer_profile_id', $profile->id)
            ->where('status', Appointment::STATUS_REQUESTED)
            ->orderBy('start_at_utc')
            ->get();

        $upcoming = Appointment::with('user')
            ->where('astrologer_profile_id', $profile->id)
            ->where('status', Appointment::STATUS_CONFIRMED)
            ->where('start_at_utc', '>=', now())
            ->orderBy('start_at_utc')
            ->get();

        if ($request->wantsJson()) {
            $map = function (Appointment $appointment) use ($tz) {
                return [
                    'id' => $appointment->id,
                    'status' => $appointment->status,
                    'start_at' => $appointment->start_at_utc->copy()->tz($tz)->toIso8601String(),
                    'end_at' => $appointment->end_at_utc->copy()->tz($tz)->toIso8601String(),
                    'notes_user' => $appointment->notes_user,
                    'user' => [
                        'id' => $appointment->user_id,
                        'display_name' => $this->maskUserName($appointment->user?->name, $appointment->user_id),
                    ],
                    'meeting_link' => $this->meetingLinkFor($appointment),
                ];
            };

            return response()->json([
                'timezone' => $tz,
                'pending' => $pending->map($map),
                'upcoming' => $upcoming->map($map),
            ]);
        }

        return view('astrologer.dashboard.appointments', compact('pending', 'upcoming', 'profile', 'tz'));
    }

    public function slots(Request $request)
    {
        $profile = $request->user()->astrologerProfile;
        if (!$profile) {
            abort(403, 'Astrologer profile not found.');
        }

        $tz = $request->input('tz', config('appointments.default_timezone', 'Asia/Kolkata'));
        $days = (int) $request->input('days', config('appointments.generate_days', 14));
        $fromInput = $request->input('from');

        $fromLocal = $fromInput ? CarbonImmutable::parse($fromInput, $tz) : CarbonImmutable::now($tz);
        $fromUtc = $fromLocal->startOfDay()->utc();
        $toUtc = $fromUtc->addDays($days);

        $slots = AppointmentSlot::forAstrologer($profile->id)
            ->inDateRange($fromUtc, $toUtc)
            ->orderBy('start_at_utc')
            ->get()
            ->map(fn (AppointmentSlot $slot) => [
                'id' => $slot->id,
                'start_at' => $slot->start_at_utc->copy()->tz($tz)->toIso8601String(),
                'end_at' => $slot->end_at_utc->copy()->tz($tz)->toIso8601String(),
                'status' => $slot->status,
            ]);

        return response()->json([
            'timezone' => $tz,
            'data' => $slots,
        ]);
    }

    public function confirm(string $id, Request $request, AppointmentService $appointments)
    {
        $profile = $request->user()->astrologerProfile;
        $appointment = Appointment::where('id', $id)
            ->where('astrologer_profile_id', $profile->id)
            ->firstOrFail();

        try {
            $appointment = $appointments->confirmAppointment($appointment, $profile);
        } catch (\Exception $e) {
            if (!$request->wantsJson()) {
                return back()->with('error', $e->getMessage());
            }
            return response()->json(['error' => $e->getMessage()], 409);
        }

        if (!$request->wantsJson()) {
            return back()->with('success', 'Appointment confirmed.');
        }

        return response()->json([
            'appointment_id' => $appointment->id,
            'status' => $appointment->status,
        ]);
    }

    public function decline(string $id, Request $request, AppointmentService $appointments)
    {
        $profile = $request->user()->astrologerProfile;
        $appointment = Appointment::where('id', $id)
            ->where('astrologer_profile_id', $profile->id)
            ->firstOrFail();

        try {
            $appointment = $appointments->declineAppointment($appointment, $profile, $request->input('reason'));
        } catch (\Exception $e) {
            if (!$request->wantsJson()) {
                return back()->with('error', $e->getMessage());
            }
            return response()->json(['error' => $e->getMessage()], 409);
        }

        if (!$request->wantsJson()) {
            return back()->with('success', 'Appointment declined.');
        }

        return response()->json([
            'appointment_id' => $appointment->id,
            'status' => $appointment->status,
        ]);
    }

    public function cancel(string $id, Request $request, AppointmentService $appointments)
    {
        $profile = $request->user()->astrologerProfile;
        $appointment = Appointment::where('id', $id)
            ->where('astrologer_profile_id', $profile->id)
            ->firstOrFail();

        try {
            $appointment = $appointments->cancelAppointment($appointment, $request->user(), $request->input('reason'));
        } catch (\Exception $e) {
            if (!$request->wantsJson()) {
                return back()->with('error', $e->getMessage());
            }
            return response()->json(['error' => $e->getMessage()], 409);
        }

        if (!$request->wantsJson()) {
            return back()->with('success', 'Appointment cancelled.');
        }

        return response()->json([
            'appointment_id' => $appointment->id,
            'status' => $appointment->status,
        ]);
    }

    public function blockSlot(string $slotId, Request $request)
    {
        $profile = $request->user()->astrologerProfile;
        $slot = AppointmentSlot::where('id', $slotId)
            ->where('astrologer_profile_id', $profile->id)
            ->firstOrFail();

        if (in_array($slot->status, [AppointmentSlot::STATUS_BOOKED, AppointmentSlot::STATUS_HELD], true)) {
            return response()->json(['error' => 'Cannot block a held or booked slot.'], 409);
        }

        $slot->block();

        return response()->json([
            'slot_id' => $slot->id,
            'status' => $slot->status,
        ]);
    }

    public function unblockSlot(string $slotId, Request $request)
    {
        $profile = $request->user()->astrologerProfile;
        $slot = AppointmentSlot::where('id', $slotId)
            ->where('astrologer_profile_id', $profile->id)
            ->firstOrFail();

        if ($slot->status !== AppointmentSlot::STATUS_BLOCKED) {
            return response()->json(['error' => 'Slot is not blocked.'], 409);
        }

        $slot->unblock();

        return response()->json([
            'slot_id' => $slot->id,
            'status' => $slot->status,
        ]);
    }

    protected function maskUserName(?string $name, int $userId): string
    {
        return 'User #' . substr((string) $userId, -4);
    }

    protected function meetingLinkFor(Appointment $appointment): ?string
    {
        if (!$appointment->meetingLink || !$appointment->isMeetingLinkVisible()) {
            return null;
        }

        return $appointment->meetingLink->join_url;
    }
}
