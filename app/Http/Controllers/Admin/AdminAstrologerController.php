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
            ->with(['astrologerProfile', 'activeMembership']);

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhereHas('astrologerProfile', function ($sub) use ($search) {
                        $sub->where('display_name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by Status
        if ($request->has('status') && $request->status !== 'all') {
            $query->whereHas('astrologerProfile', function ($q) use ($request) {
                $q->where('verification_status', $request->status);
            });
        }

        // Export
        if ($request->input('export') === 'csv') {
            return $this->exportCsv($query);
        }

        $astrologers = $query->latest()->paginate(20)->withQueryString();

        return view('admin.astrologers.index', compact('astrologers'));
    }

    public function show($id)
    {
        // Accept ID directly to avoid route key binding issues if mixed with User model
        $astrologer = User::with(['astrologerProfile.documents', 'astrologerProfile.availabilityRules', 'astrologerProfile.reviews'])->findOrFail($id);
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
        $oldStatus = $profile->verification_status;

        $updateData = [
            'verification_status' => $request->status,
            'rejection_reason' => $request->status === 'rejected' ? $request->rejection_reason : null,
            'is_verified' => $request->status === 'approved',
            'verified_at' => $request->status === 'approved' ? now() : null,
            'verified_by_admin_id' => $request->status === 'approved' ? Auth::id() : null,
        ];

        $profile->update($updateData);

        \App\Services\AdminActivityLogger::log('astrologer.verified', $user, [
            'status_from' => $oldStatus,
            'status_to' => $request->status,
            'reason' => $request->rejection_reason
        ]);

        return back()->with('success', 'Verification status updated.');
    }

    public function toggleVisibility(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $profile = $user->astrologerProfile;

        $profile->update([
            'show_on_front' => !$profile->show_on_front
        ]);

        \App\Services\AdminActivityLogger::log('astrologer.visiblity_toggled', $user, [
            'show_on_front' => $profile->show_on_front
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

        \App\Services\AdminActivityLogger::log('astrologer.enabled_toggled', $user, [
            'is_enabled' => $profile->is_enabled
        ]);

        return back()->with('success', 'Account status toggled.');
    }

    private function exportCsv($query)
    {
        $filename = 'astrologers_export_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($query) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Name', 'Email', 'Phone', 'Verification', 'Call Rate', 'Chat Rate', 'Status']);

            $query->chunk(100, function ($users) use ($file) {
                foreach ($users as $user) {
                    $profile = $user->astrologerProfile;
                    fputcsv($file, [
                        $user->id,
                        $user->name,
                        $user->email,
                        $user->phone,
                        $profile->verification_status ?? 'N/A',
                        $profile->call_per_minute ?? 0,
                        $profile->chat_per_minute ?? 0,
                        $user->is_active ? 'Active' : 'Inactive'
                    ]);
                }
            });
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id',
            'action' => 'required|in:verify_approve,verify_reject,enable_front,disable_front,export',
        ], [
            'ids.required' => 'Please select at least one astrologer.',
            'action.required' => 'Please select an action.',
        ]);

        $ids = $request->ids;
        $action = $request->action;

        if ($action === 'export') {
            return $this->exportCsv(new Request(['ids' => $ids]));
        }

        $count = 0;
        $users = User::whereIn('id', $ids)->with('astrologerProfile')->get();

        foreach ($users as $user) {
            $profile = $user->astrologerProfile;
            if (!$profile)
                continue;

            switch ($action) {
                case 'verify_approve':
                    if ($profile->verification_status !== 'verified') {
                        $profile->update(['verification_status' => 'verified']);
                        \App\Services\AdminActivityLogger::log('astrologer_verified_bulk', $user);
                        $count++;
                    }
                    break;
                case 'verify_reject':
                    if ($profile->verification_status !== 'rejected') {
                        $profile->update(['verification_status' => 'rejected']);
                        \App\Services\AdminActivityLogger::log('astrologer_rejected_bulk', $user);
                        $count++;
                    }
                    break;
                case 'enable_front':
                    if (!$user->is_active) {
                        $user->update(['is_active' => true]);
                        \App\Services\AdminActivityLogger::log('astrologer_activated_bulk', $user);
                        $count++;
                    }
                    break;
                case 'disable_front':
                    if ($user->is_active) {
                        $user->update(['is_active' => false]);
                        \App\Services\AdminActivityLogger::log('astrologer_deactivated_bulk', $user);
                        $count++;
                    }
                    break;
            }
        }

        return back()->with('success', "Bulk action applied to {$count} astrologers.");
    }
}
