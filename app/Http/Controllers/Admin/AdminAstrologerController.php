<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAstrologerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::role('Astrologer')
            ->whereHas('astrologerProfile')
            ->with('astrologerProfile');

        // Filter by Status
        if ($request->has('status') && $request->status !== 'all') {
            $query->whereHas('astrologerProfile', function ($q) use ($request) {
                $q->where('verification_status', $request->status);
            });
        }

        $astrologers = $query->latest()->paginate(20);

        return view('admin.astrologers.index', compact('astrologers'));
    }

    public function show($id)
    {
        // Accept ID directly to avoid route key binding issues if mixed with User model
        $astrologer = User::with(['astrologerProfile.documents', 'astrologerProfile.availabilityRules'])->findOrFail($id);
        return view('admin.astrologers.show', compact('astrologer'));
    }

    public function verify(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,pending',
            'rejection_reason' => 'nullable|string|required_if:status,rejected',
        ]);

        $user = User::findOrFail($id);
        $profile = $user->astrologerProfile;

        $updateData = [
            'verification_status' => $request->status,
            'rejection_reason' => $request->rejection_reason,
            'is_verified' => $request->status === 'approved',
            'verified_at' => $request->status === 'approved' ? now() : null,
            'verified_by_admin_id' => $request->status === 'approved' ? Auth::id() : null,
        ];

        // Auto-enable frontend visibility on approval? User rule says: "show_on_front optional"
        // Let's default to false and let admin toggle it separately to be safe, or just auto-enable.
        // Rule: "approve (sets is_verified=true... show_on_front optional)"
        // We will separate the toggle.

        $profile->update($updateData);

        return back()->with('success', 'Verification status updated.');
    }

    public function toggleVisibility(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $profile = $user->astrologerProfile;

        $profile->update([
            'show_on_front' => !$profile->show_on_front
        ]);

        return back()->with('success', 'Visibility toggled.');
    }

    public function toggleAccount(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $profile = $user->astrologerProfile;

        $profile->update([
            'is_enabled' => !$profile->is_enabled
        ]);

        return back()->with('success', 'Account status toggled.');
    }
}
