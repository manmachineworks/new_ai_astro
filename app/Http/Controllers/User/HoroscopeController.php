<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

class HoroscopeController extends Controller
{
    public function index()
    {
        $user = [
            'dob' => '1995-08-15',
            'tob' => '14:30:00',
            'pob' => 'Mumbai, Maharashtra'
        ];

        $horoscope = [
            'daily' => 'Today brings clarity in communication. It is a good time to express your feelings and resolve misunderstandings.',
            'weekly' => 'Focus on personal growth this week. New learning opportunities will arise.',
            'monthly' => 'Career advancements are likely this month. Stay dedicated and consistent.'
        ];

        $reports = [
            [
                'id' => 1,
                'title' => 'Detailed Birth Chart',
                'category' => 'Natal',
                'description' => 'Complete analysis of your planetary positions at the time of birth.',
                'date' => 'Generated on Oct 01',
                'price' => 0,
                'purchased' => true
            ],
            [
                'id' => 2,
                'title' => '2024 Yearly Prediction',
                'category' => 'Forecast',
                'description' => 'In-depth look at what the coming year holds for you.',
                'date' => 'Available',
                'price' => 499,
                'purchased' => false
            ],
            [
                'id' => 3,
                'title' => 'Career & Wealth Report',
                'category' => 'Financial',
                'description' => 'Specific guidance on financial growth and career path.',
                'date' => 'Available',
                'price' => 299,
                'purchased' => false
            ]
        ];

        return view('user.horoscope.index', compact('user', 'horoscope', 'reports'));
    }

    public function show($type)
    {
        // View specific report or horoscope type
        return view('user.horoscope.show', compact('type'));
    }
}
