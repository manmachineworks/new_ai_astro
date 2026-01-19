<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserProfile;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $user->load('profile'); // Ensure profile is loaded

        $verification = [
            'email_verified' => !is_null($user->email_verified_at),
            'phone_verified' => !is_null($user->firebase_uid),
        ];

        // Accessing KYC from meta field in UserProfile or User if no dedicated table
        // For now, defaulting to placeholder if not present
        $kycData = $user->profile->meta['kyc'] ?? [
            'status' => 'pending',
            'documents' => [
                'aadhaar_front' => 'pending',
                'aadhaar_back' => 'pending',
                'pan_card' => 'pending'
            ]
        ];

        $kyc = $kycData;

        return view('user.profile.index', compact('user', 'verification', 'kyc'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            // Profile fields
            'gender' => 'nullable|in:male,female,other',
            'dob' => 'nullable|date',
            'location' => 'nullable|string|max:255',
        ]);

        // Update User Table
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        // Update UserProfile Table
        // Use updateOrCreate in case profile doesn't exist yet
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'name' => $request->name, // Keeping in sync or specific display name
                'gender' => $request->gender,
                'dob' => $request->dob,
                'location' => $request->location,
            ]
        );

        return back()->with('success', 'Profile updated successfully!');
    }

    public function verifyPhone(Request $request)
    {
        // Integration with SMS Provider (e.g. Twilio/Msg91)
        // For now, we simulate success or checking logical flow
        return back()->with('success', 'OTP sent to mobile (Simulation).');
    }

    public function verifyEmail(Request $request)
    {
        // Trigger generic EmailVerificationNotification
        if (auth()->user()->hasVerifiedEmail()) {
            return back()->with('info', 'Email already verified.');
        }

        auth()->user()->sendEmailVerificationNotification();

        return back()->with('success', 'Verification link sent.');
    }
}
