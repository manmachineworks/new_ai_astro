<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\AstrologerProfile;
use App\Services\AppointmentService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Appointment::with(['user', 'astrologerProfile.user', 'meetingLink'])
            ->orderByDesc('start_at_utc');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('astrologer_id')) {
            $query->where('astrologer_profile_id', $request->input('astrologer_id'));
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $tz = config('appointments.default_timezone', 'Asia/Kolkata');
            $startUtc = Carbon::parse($request->input('start_date'), $tz)->startOfDay()->setTimezone('UTC');
            $endUtc = Carbon::parse($request->input('end_date'), $tz)->endOfDay()->setTimezone('UTC');
            $query->whereBetween('start_at_utc', [$startUtc, $endUtc]);
        }

        $appointments = $query->paginate(20);
        $astrologers = AstrologerProfile::with('user')->orderBy('display_name')->get();

        return view('admin.appointments.index', compact('appointments', 'astrologers'));
    }

    public function show(string $id)
    {
        $appointment = Appointment::with([
            'user',
            'astrologerProfile.user',
            'walletHold',
            'meetingLink',
        ])->findOrFail($id);

        $events = $appointment->events()->orderBy('created_at')->get();

        return view('admin.appointments.show', compact('appointment', 'events'));
    }

    public function cancel(Request $request, string $id, AppointmentService $appointments)
    {
        $appointment = Appointment::findOrFail($id);
        $appointments->cancelAppointment($appointment, $request->user(), $request->input('reason'));

        return redirect()->back()->with('success', 'Appointment cancelled and refund processed.');
    }
}
