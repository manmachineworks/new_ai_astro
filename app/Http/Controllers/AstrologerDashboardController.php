<?php

namespace App\Http\Controllers;

use App\Models\AstrologerProfile;
use App\Models\AvailabilityRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AstrologerDashboardController extends Controller
{
    protected function getProfile()
    {
        $user = Auth::user();
        if (!$user->hasRole('Astrologer')) {
            abort(403, 'Unauthorized');
        }

        // Ensure profile exists (it should via observer or registration, but safe check)
        return AstrologerProfile::firstOrCreate(['user_id' => $user->id]);
    }

    public function editProfile()
    {
        $profile = $this->getProfile();
        return view('astrologer.dashboard.profile', compact('profile'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'display_name' => 'required|string|max:255',
            'bio' => 'required|string|max:1000',
            'experience_years' => 'required|integer|min:0',
            'languages' => 'array',
            'skills' => 'array',
        ]);

        $profile = $this->getProfile();
        $profile->update($request->only([
            'display_name',
            'bio',
            'experience_years',
            'languages',
            'skills',
            'specialties',
            'gender',
            'dob'
        ]));

        if ($request->hasFile('profile_photo')) {
            // $path = $request->file('profile_photo')->store('public/astrologers');
            // Mock storage for now or use public path
            // $profile->update(['profile_photo_path' => Storage::url($path)]);
        }

        return back()->with('success', 'Profile updated.');
    }

    public function editServices()
    {
        $profile = $this->getProfile();
        return view('astrologer.dashboard.services', compact('profile'));
    }

    public function updateServices(Request $request)
    {
        $request->validate([
            'call_per_minute' => 'required|numeric|min:0',
            'chat_per_session' => 'required|numeric|min:0',
        ]);

        $profile = $this->getProfile();

        // Cannot enable if not verified
        if (!$profile->is_verified) {
            // Allow price update but force disable if verified is false? 
            // Logic: They can update prices, but toggles might be locked in UI.
            // Here we just save what comes, UI handles disabled state.
        }

        $profile->update([
            'call_per_minute' => $request->call_per_minute,
            'chat_per_session' => $request->chat_per_session,
            'is_call_enabled' => $request->has('is_call_enabled') && $profile->is_verified,
            'is_chat_enabled' => $request->has('is_chat_enabled') && $profile->is_verified,
        ]);

        return back()->with('success', 'Services & Pricing updated.');
    }

    public function editAvailability()
    {
        $profile = $this->getProfile();
        $rules = $profile->availabilityRules()->get()->keyBy('day_of_week');
        return view('astrologer.dashboard.availability', compact('profile', 'rules'));
    }

    public function updateAvailability(Request $request)
    {
        $profile = $this->getProfile();

        // Rules: array of [day => [start, end, active]]
        $inputs = $request->input('schedule', []);

        foreach ($inputs as $day => $data) {
            AvailabilityRule::updateOrCreate(
                ['astrologer_profile_id' => $profile->id, 'day_of_week' => $day],
                [
                    'start_time_utc' => $data['start'] ?? '09:00',
                    'end_time_utc' => $data['end'] ?? '17:00',
                    'is_active' => isset($data['active']),
                ]
            );
        }

        return back()->with('success', 'Availability updated.');
    }

    public function calls()
    {
        $profile = $this->getProfile();
        $calls = $profile->callSessions()->latest()->paginate(15);
        return view('astrologer.dashboard.calls', compact('calls'));
    }
}
