<?php

namespace App\Http\Controllers;

use App\Models\AstrologerProfile;
use App\Models\AvailabilityRule;
use App\Models\CallSession;
use App\Models\ChatSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

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

    public function index()
    {
        $user = Auth::user();
        $profile = $this->getProfile();

        $callsQuery = $this->buildCallSessionsQuery($profile, $user);
        $chatsQuery = $this->buildChatSessionsQuery($profile, $user);

        $stats = [
            'total_calls' => (clone $callsQuery)->count(),
            'total_chats' => (clone $chatsQuery)->count(),
        ];

        $recentCalls = (clone $callsQuery)->latest()->limit(5)->get();
        if (!Schema::hasColumn('call_sessions', 'cost') && Schema::hasColumn('call_sessions', 'gross_amount')) {
            foreach ($recentCalls as $call) {
                $call->cost = $call->gross_amount;
            }
        }

        $recentChats = (clone $chatsQuery)->latest()->limit(5)->get();

        return view('astrologer.dashboard', compact('user', 'stats', 'recentCalls', 'recentChats'));
    }

    public function updateDashboardLayout(Request $request)
    {
        $request->validate([
            'widgets' => 'required|array',
        ]);

        $profile = $this->getProfile();
        $settings = $profile->dashboard_settings ?? [];
        $settings['widgets'] = $request->widgets;

        $profile->dashboard_settings = $settings;
        $profile->save();

        return response()->json(['status' => 'success']);
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

    public function earnings()
    {
        $profile = $this->getProfile();
        $ledger = $profile->earningsLedger()->latest()->paginate(20);

        // Aggregates
        $totalEarnings = $profile->earningsLedger()->where('amount', '>', 0)->sum('amount');
        $monthEarnings = $profile->earningsLedger()->where('amount', '>', 0)->whereMonth('created_at', now()->month)->sum('amount');
        $todayEarnings = $profile->earningsLedger()->where('amount', '>', 0)->whereDate('created_at', today())->sum('amount');

        return view('astrologer.dashboard.earnings', compact('ledger', 'totalEarnings', 'monthEarnings', 'todayEarnings'));
    }

    public function toggleStatus(Request $request)
    {
        $request->validate(['status' => 'required|in:online,offline']);

        $profile = $this->getProfile();
        // Assuming 'is_online' or 'is_active' column exists on profile/user. 
        // Based on previous code, likely on profile if specifically for availability.
        // For now, mapping to existing is_active on user or profile.
        // Let's assume we added is_online to astrologer_profiles or use user->is_active.

        // checking migration... we didn't add is_online manually, but it might be there.
        // User previous code used $user->is_active.

        $user = Auth::user();
        if ($request->status == 'online') {
            $user->is_active = true;
        } else {
            $user->is_active = false;
        }
        $user->save();

        return back()->with('success', $user->is_active ? 'You are now online.' : 'You are now offline.');
    }

    private function buildCallSessionsQuery(AstrologerProfile $profile, $user)
    {
        $query = CallSession::query();

        if (Schema::hasColumn('call_sessions', 'astrologer_profile_id')) {
            return $query->where('astrologer_profile_id', $profile->id);
        }

        if (Schema::hasColumn('call_sessions', 'astrologer_user_id')) {
            return $query->where('astrologer_user_id', $user->id);
        }

        return $query->whereRaw('1=0');
    }

    private function buildChatSessionsQuery(AstrologerProfile $profile, $user)
    {
        $query = ChatSession::query();

        if (Schema::hasColumn('chat_sessions', 'astrologer_profile_id')) {
            return $query->where('astrologer_profile_id', $profile->id);
        }

        if (Schema::hasColumn('chat_sessions', 'astrologer_user_id')) {
            return $query->where('astrologer_user_id', $user->id);
        }

        return $query->whereRaw('1=0');
    }
}
