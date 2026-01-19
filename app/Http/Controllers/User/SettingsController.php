<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserPreference;

class SettingsController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $preferences = $user->preferences ?? new UserPreference();

        return view('user.settings.index', compact('user', 'preferences'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'preferred_languages' => 'array',
            'preferred_specialties' => 'array',
            // 'zodiac_sign' => 'string' // If applicable
        ]);

        $user = auth()->user();

        $user->preferences()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'preferred_languages' => $request->preferred_languages,
                'preferred_specialties' => $request->preferred_specialties,
                // Add other fields as per form
            ]
        );

        return back()->with('success', 'Settings updated successfully!');
    }
}
