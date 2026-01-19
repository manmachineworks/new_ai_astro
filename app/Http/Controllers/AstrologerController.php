<?php

namespace App\Http\Controllers;

use App\Models\AstrologerProfile;
use App\Models\AstrologerPricingHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AstrologerController extends Controller
{
    /**
     * List Astrologers (Search & Filter)
     */
    public function index(Request $request)
    {
        $query = User::role('Astrologer')
            ->where('is_active', true)
            ->whereHas('astrologerProfile', function ($q) {
                $q->where('visibility', true);
            })
            ->with(['astrologerProfile']);

        // Search by Name or Phone
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by Skills (JSON)
        if ($request->has('skill')) {
            $skill = $request->skill; // e.g., "Vedic"
            // Use JSON_CONTAINS or LIKE for simpler JSON array search
            $query->whereHas('astrologerProfile', function ($q) use ($skill) {
                $q->whereJsonContains('skills', $skill);
            });
        }

        // Filter by Language
        if ($request->has('language')) {
            $lang = $request->language;
            $query->whereHas('astrologerProfile', function ($q) use ($lang) {
                $q->whereJsonContains('languages', $lang);
            });
        }

        $astrologers = $query->paginate(20);

        return response()->json($astrologers);
    }

    /**
     * Get Public Profile
     */
    public function show($id)
    {
        $astrologer = User::role('Astrologer')
            ->where('id', $id)
            ->with(['astrologerProfile'])
            ->firstOrFail();

        return response()->json($astrologer);
    }

    /**
     * Update Profile (By Astrologer)
     */
    public function update(Request $request)
    {
        $user = $request->user();

        /*
        // Ensure user is Astrologer
        if (!$user->hasRole('Astrologer')) {
             return response()->json(['message' => 'Unauthorized'], 403);
        }
        */
        // Or middleware handles it.

        $validated = $request->validate([
            'bio' => 'nullable|string',
            'skills' => 'nullable|array',
            'languages' => 'nullable|array',
            'experience_years' => 'nullable|integer',
            'call_per_minute' => 'nullable|numeric|min:0',
            'chat_per_session' => 'nullable|numeric|min:0',
            'availability_schedule' => 'nullable|array', // Validate structure in a real app
        ]);

        $existingProfile = $user->astrologerProfile;
        $oldCall = $existingProfile?->call_per_minute;
        $oldChat = $existingProfile?->chat_per_session;

        $profile = $user->astrologerProfile()->updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        $newCall = $profile->call_per_minute;
        $newChat = $profile->chat_per_session;

        if ($oldCall != $newCall || $oldChat != $newChat) {
            AstrologerPricingHistory::create([
                'astrologer_profile_id' => $profile->id,
                'old_call_per_minute' => $oldCall,
                'new_call_per_minute' => $newCall,
                'old_chat_per_session' => $oldChat,
                'new_chat_per_session' => $newChat,
                'changed_by_user_id' => $user->id,
                'change_source' => 'astrologer',
            ]);
        }

        return response()->json($profile);
    }

    /**
     * Toggle Availability
     */
    public function toggleStatus(Request $request)
    {
        $request->validate([
            'is_call_enabled' => 'boolean',
            'is_chat_enabled' => 'boolean',
            'is_appointment_enabled' => 'boolean',
            'visibility' => 'boolean',
        ]);

        $user = $request->user();
        $user->astrologerProfile()->update($request->only([
            'is_call_enabled',
            'is_chat_enabled',
            'is_appointment_enabled',
            'visibility'
        ]));

        return response()->json(['message' => 'Status updated']);
    }
}
