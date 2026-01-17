<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\AppointmentSlot;
use App\Services\AppointmentService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function listSlots(Request $request, string $astrologerId)
    {
        return $this->slots($request, $astrologerId);
    }

    public function slots(Request $request, string $astrologerId)
    {
        $tz = $this->resolveTimezone($request);
        $days = (int) $request->input('days', config('appointments.generate_days', 14));
        $fromInput = $request->input('from');

        $fromLocal = $fromInput ? CarbonImmutable::parse($fromInput, $tz) : CarbonImmutable::now($tz);
        $fromUtc = $fromLocal->startOfDay()->utc();
        $toUtc = $fromUtc->addDays($days);

        $slots = AppointmentSlot::available()
            ->where('astrologer_profile_id', $astrologerId)
            ->whereBetween('start_at_utc', [$fromUtc, $toUtc])
            ->orderBy('start_at_utc')
            ->get();

        return response()->json([
            'timezone' => $tz,
            'data' => $slots->map(fn ($slot) => $this->formatSlot($slot, $tz)),
        ]);
    }

    public function hold(Request $request, AppointmentService $appointments)
    {
        $validated = $request->validate([
            'astrologer_id' => ['required', 'exists:astrologer_profiles,id'],
            'slot_id' => ['nullable', 'uuid', 'exists:appointment_slots,id'],
            'start_at' => ['nullable', 'date'],
            'end_at' => ['nullable', 'date'],
            'tz' => ['nullable', 'string'],
        ]);

        $tz = $this->resolveTimezone($request);

        if (!empty($validated['slot_id'])) {
            $slot = AppointmentSlot::findOrFail($validated['slot_id']);
        } else {
            if (empty($validated['start_at']) || empty($validated['end_at'])) {
                return response()->json(['error' => 'start_at and end_at are required when slot_id is not provided.'], 422);
            }

            $startAtUtc = CarbonImmutable::parse($validated['start_at'], $tz)->utc();
            $endAtUtc = CarbonImmutable::parse($validated['end_at'], $tz)->utc();

            $slot = AppointmentSlot::where('astrologer_profile_id', $validated['astrologer_id'])
                ->where('start_at_utc', $startAtUtc)
                ->where('end_at_utc', $endAtUtc)
                ->firstOrFail();
        }

        if ($slot->astrologer_profile_id !== $validated['astrologer_id']) {
            return response()->json(['error' => 'Slot does not belong to astrologer.'], 422);
        }

        if ($slot->start_at_utc->isPast()) {
            return response()->json(['error' => 'Slot is in the past.'], 422);
        }

        try {
            $hold = $appointments->holdSlot($slot, $request->user());
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 409);
        }

        return response()->json([
            'appointment_id' => $hold['appointment']?->id,
            'slot_id' => $hold['slot_id'],
            'hold_id' => $hold['hold_id'],
            'expires_at' => optional($hold['expires_at'])->toIso8601String(),
            'amount_held' => $hold['amount_held'],
            'already_held' => $hold['already_held'],
        ]);
    }

    public function holdSlot(Request $request, AppointmentService $appointments)
    {
        return $this->hold($request, $appointments);
    }

    public function store(Request $request, AppointmentService $appointments)
    {
        $validated = $request->validate([
            'slot_id' => ['required', 'uuid', 'exists:appointment_slots,id'],
            'hold_id' => ['required', 'uuid', 'exists:wallet_holds,id'],
            'notes_user' => ['nullable', 'string', 'max:2000'],
        ]);

        $slot = AppointmentSlot::findOrFail($validated['slot_id']);

        try {
            $appointment = $appointments->createAppointment(
                $slot,
                $request->user(),
                $validated['hold_id'],
                $validated['notes_user'] ?? null
            );
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 409);
        }

        return response()->json([
            'appointment_id' => $appointment->id,
            'status' => $appointment->status,
        ]);
    }

    public function cancel(Request $request, string $id, AppointmentService $appointments)
    {
        $appointment = Appointment::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $appointment = $appointments->cancelAppointment($appointment, $request->user(), $request->input('reason'));

        return response()->json([
            'appointment_id' => $appointment->id,
            'status' => $appointment->status,
        ]);
    }

    public function index(Request $request)
    {
        $tz = $this->resolveTimezone($request);
        $appointments = Appointment::with('astrologerProfile')
            ->where('user_id', $request->user()->id)
            ->latest('start_at_utc')
            ->paginate(20);

        if (!$request->wantsJson()) {
            return view('user.appointments.index', compact('appointments', 'tz'));
        }

        return response()->json([
            'timezone' => $tz,
            'data' => $appointments->through(function (Appointment $appointment) use ($tz) {
                return [
                    'id' => $appointment->id,
                    'status' => $appointment->status,
                    'start_at' => $appointment->start_at_utc->copy()->tz($tz)->toIso8601String(),
                    'end_at' => $appointment->end_at_utc->copy()->tz($tz)->toIso8601String(),
                    'start_at_utc' => $appointment->start_at_utc->toIso8601String(),
                    'end_at_utc' => $appointment->end_at_utc->toIso8601String(),
                    'pricing_mode' => $appointment->pricing_mode,
                    'price_total' => $appointment->price_total,
                ];
            }),
        ]);
    }

    public function show(Request $request, string $id)
    {
        $tz = $this->resolveTimezone($request);

        $appointment = Appointment::with(['astrologerProfile.user', 'meetingLink'])
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if (!$request->wantsJson()) {
            return view('user.appointments.show', compact('appointment'));
        }

        $meetingLink = null;
        if ($appointment->status === Appointment::STATUS_CONFIRMED && $appointment->isMeetingLinkVisible()) {
            $meetingLink = $appointment->meetingLink?->join_url;
        }

        return response()->json([
            'id' => $appointment->id,
            'status' => $appointment->status,
            'astrologer_profile_id' => $appointment->astrologer_profile_id,
            'astrologer_name' => $appointment->astrologerProfile->display_name ?? 'Astrologer',
            'start_at' => $appointment->start_at_utc->copy()->tz($tz)->toIso8601String(),
            'end_at' => $appointment->end_at_utc->copy()->tz($tz)->toIso8601String(),
            'start_at_utc' => $appointment->start_at_utc->toIso8601String(),
            'end_at_utc' => $appointment->end_at_utc->toIso8601String(),
            'pricing_mode' => $appointment->pricing_mode,
            'price_total' => $appointment->price_total,
            'notes_user' => $appointment->notes_user,
            'notes_astrologer' => $appointment->notes_astrologer,
            'meeting_link' => $meetingLink,
        ]);
    }

    protected function resolveTimezone(Request $request): string
    {
        return $request->input('tz', config('appointments.default_timezone', 'Asia/Kolkata'));
    }

    protected function formatSlot(AppointmentSlot $slot, string $tz): array
    {
        return [
            'id' => $slot->id,
            'start_at' => $slot->start_at_utc->copy()->tz($tz)->toIso8601String(),
            'end_at' => $slot->end_at_utc->copy()->tz($tz)->toIso8601String(),
            'start_at_utc' => $slot->start_at_utc->toIso8601String(),
            'end_at_utc' => $slot->end_at_utc->toIso8601String(),
            'duration_minutes' => $slot->duration_minutes,
            'status' => $slot->status,
        ];
    }
}
