<?php

namespace App\Http\Controllers\Astrologer;

use App\Http\Requests\Astrologer\UpdateAppointmentNotesRequest;
use App\Models\Appointment;
use Inertia\Inertia;

class AppointmentController extends AstrologerBaseController
{
    public function index()
    {
        $user = auth()->user();
        $appointments = Appointment::where('astrologer_user_id', $user->id)
            ->latest()
            ->paginate(12);

        return Inertia::render('Astrologer/Appointments/Index', [
            'appointments' => $appointments,
        ]);
    }

    public function accept(Appointment $appointment)
    {
        $this->assertOwn($appointment);
        $appointment->update(['status' => 'confirmed']);

        return back()->with('success', 'Appointment accepted.');
    }

    public function reject(Appointment $appointment)
    {
        $this->assertOwn($appointment);
        $appointment->update(['status' => 'declined']);

        return back()->with('success', 'Appointment rejected.');
    }

    public function updateNotes(UpdateAppointmentNotesRequest $request, Appointment $appointment)
    {
        $this->assertOwn($appointment);
        $appointment->update(['notes' => $request->notes]);

        return back()->with('success', 'Notes saved.');
    }

    protected function assertOwn(Appointment $appointment): void
    {
        abort_unless(auth()->id() === $appointment->astrologer_user_id, 403);
    }
}
