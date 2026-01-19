<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index()
    {
        $upcoming = [
            [
                'id' => 101,
                'astrologer_name' => 'Astro Priya',
                'astrologer_image' => null,
                'date' => 'Tomorrow, Oct 28',
                'time' => '10:00 AM',
                'status' => 'upcoming',
            ]
        ];

        $past = [
            [
                'id' => 99,
                'astrologer_name' => 'Dr. Sharma',
                'astrologer_image' => null,
                'date' => 'Oct 20, 2023',
                'time' => '2:00 PM',
                'status' => 'completed',
            ],
            [
                'id' => 98,
                'astrologer_name' => 'Guruji',
                'astrologer_image' => null,
                'date' => 'Oct 15, 2023',
                'time' => '4:00 PM',
                'status' => 'cancelled',
            ]
        ];

        return view('user.appointments.index', compact('upcoming', 'past'));
    }

    public function book(Request $request, $astrologerId)
    {
        $astrologer = [
            'id' => $astrologerId,
            'name' => 'Astro Priya',
            'image' => null,
            'fee' => 500.00
        ];

        $walletBalance = $request->user()->wallet_balance ?? 150.00;

        return view('user.appointments.book', compact('astrologer', 'walletBalance'));
    }

    public function confirm(Request $request)
    {
        // Guardrail & Logic
        // ...
        return redirect()->route('user.appointments.index')->with('success', 'Appointment confirmed successfully!');
    }
}
