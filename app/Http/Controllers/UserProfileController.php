<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    public function show(Request $request)
    {
        $profile = $request->user()->profile;

        return response()->json([
            'profile' => $profile,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'gender' => 'nullable|string|in:male,female,other',
            'dob' => 'nullable|date',
            'location' => 'nullable|string|max:255',
            'meta' => 'nullable|array',
        ]);

        $user = $request->user();
        $profile = $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        if (!empty($validated['name']) && $user->name !== $validated['name']) {
            $user->update(['name' => $validated['name']]);
        }

        return response()->json([
            'profile' => $profile,
        ]);
    }
}
