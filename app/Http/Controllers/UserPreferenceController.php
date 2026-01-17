<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserPreference;
use Illuminate\Support\Facades\Auth;

class UserPreferenceController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $preferences = $user->preferences()->firstOrNew();

        // Mock data for specific options (in real app, fetch from constants/DB)
        $allLanguages = ['English', 'Hindi', 'Marathi', 'Gujarati', 'Tamil', 'Bengali'];
        $allSpecialties = ['Vedic', 'Tarot', 'Numerology', 'Vastu', 'Face Reading', 'Palmistry', 'Love Helper'];

        return view('user.preferences', compact('preferences', 'allLanguages', 'allSpecialties'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'languages' => 'nullable|array',
            'specialties' => 'nullable|array',
            'budget_min' => 'nullable|numeric|min:0',
            'budget_max' => 'nullable|numeric|gte:budget_min',
            'zodiac_sign' => 'nullable|string'
        ]);

        $user = Auth::user();

        $user->preferences()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'preferred_languages' => $request->languages ?? ['English'],
                'preferred_specialties' => $request->specialties ?? [],
                'preferred_price_range' => [
                    'min' => $request->budget_min ?? 0,
                    'max' => $request->budget_max ?? 999
                ],
                'zodiac_sign' => $request->zodiac_sign,
                'onboarding_completed' => true
            ]
        );

        return redirect()->route('home')->with('success', 'Preferences updated! Your feed will now be personalized.');
    }
}
