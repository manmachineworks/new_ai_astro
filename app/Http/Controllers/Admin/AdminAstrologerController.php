<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AvailabilityException;
use App\Models\AvailabilityRule;
use App\Models\AstrologerEarningsLedger;
use App\Models\CallSession;
use App\Models\ChatSession;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

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
        if ($request->filled('visible')) {
            $query->whereHas('astrologerProfile', function ($q) use ($request) {
                $q->where('show_on_front', (bool) $request->visible);
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
        $profile = $astrologer->astrologerProfile;

        $callStats = CallSession::where('astrologer_profile_id', $profile->id)
            ->selectRaw("
                COUNT(*) as total_calls,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_calls,
                SUM(CASE WHEN status IN ('missed', 'rejected', 'failed') THEN 1 ELSE 0 END) as missed_calls,
                COALESCE(SUM(billable_minutes), 0) as total_minutes,
                COALESCE(SUM(gross_amount), 0) as gross_revenue
            ")
            ->first();

        $chatStats = ChatSession::where('astrologer_profile_id', $profile->id)
            ->selectRaw("
                COUNT(*) as total_chats,
                COALESCE(SUM(total_messages_user + total_messages_astrologer), 0) as total_messages,
                COALESCE(SUM(cost), 0) as gross_revenue
            ")
            ->first();

        $earningsSummary = AstrologerEarningsLedger::where('astrologer_profile_id', $profile->id)
            ->selectRaw('COALESCE(SUM(amount), 0) as total_earned')
            ->first();

        $availabilityExceptions = AvailabilityException::where('astrologer_profile_id', $profile->id)
            ->orderByDesc('date')
            ->limit(20)
            ->get();

        $pricingHistory = Schema::hasTable('astrologer_pricing_histories')
            ? $profile->pricingHistories()->latest()->limit(20)->get()
            : collect();

        return view('admin.astrologers.show', compact(
            'astrologer',
            'profile',
            'callStats',
            'chatStats',
            'earningsSummary',
            'availabilityExceptions',
            'pricingHistory'
        ));
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

    public function updateProfile(Request $request, $id)
    {
        $request->validate([
            'display_name' => 'nullable|string|max:255',
            'experience_years' => 'nullable|integer|min:0|max:80',
            'skills' => 'nullable|string|max:1000',
            'languages' => 'nullable|string|max:1000',
            'bio' => 'nullable|string|max:2000',
        ]);

        $user = User::findOrFail($id);
        $profile = $user->astrologerProfile;

        $profile->update([
            'display_name' => $request->input('display_name', $profile->display_name),
            'experience_years' => $request->input('experience_years', $profile->experience_years),
            'skills' => $request->filled('skills') ? array_filter(array_map('trim', explode(',', $request->skills))) : $profile->skills,
            'languages' => $request->filled('languages') ? array_filter(array_map('trim', explode(',', $request->languages))) : $profile->languages,
            'bio' => $request->input('bio', $profile->bio),
        ]);

        \App\Services\AdminActivityLogger::log('astrologer.profile_updated', $user);

        return back()->with('success', 'Profile details updated.');
    }

    public function updateServices(Request $request, $id)
    {
        $request->validate([
            'call_per_minute' => 'nullable|numeric|min:0',
            'chat_per_session' => 'nullable|numeric|min:0',
            'is_call_enabled' => 'nullable|boolean',
            'is_chat_enabled' => 'nullable|boolean',
            'is_sms_enabled' => 'nullable|boolean',
            'is_appointment_enabled' => 'nullable|boolean',
        ]);

        $user = User::findOrFail($id);
        $profile = $user->astrologerProfile;

        $profile->update([
            'call_per_minute' => $request->input('call_per_minute', $profile->call_per_minute),
            'chat_per_session' => $request->input('chat_per_session', $profile->chat_per_session),
            'is_call_enabled' => $request->boolean('is_call_enabled'),
            'is_chat_enabled' => $request->boolean('is_chat_enabled'),
            'is_sms_enabled' => $request->boolean('is_sms_enabled'),
            'is_appointment_enabled' => $request->boolean('is_appointment_enabled'),
        ]);

        \App\Services\AdminActivityLogger::log('astrologer.services_updated', $user);

        return back()->with('success', 'Services and pricing updated.');
    }

    public function storeAvailabilityRule(Request $request, $id)
    {
        $request->validate([
            'day_of_week' => 'required|integer|min:0|max:6',
            'start_time_utc' => 'required|date_format:H:i',
            'end_time_utc' => 'required|date_format:H:i|after:start_time_utc',
        ]);

        $user = User::findOrFail($id);
        $profile = $user->astrologerProfile;

        AvailabilityRule::create([
            'astrologer_profile_id' => $profile->id,
            'day_of_week' => $request->day_of_week,
            'start_time_utc' => $request->start_time_utc,
            'end_time_utc' => $request->end_time_utc,
            'is_active' => true,
        ]);

        \App\Services\AdminActivityLogger::log('astrologer.availability_rule_created', $user);

        return back()->with('success', 'Availability slot added.');
    }

    public function deleteAvailabilityRule($id, $ruleId)
    {
        $user = User::findOrFail($id);
        $profile = $user->astrologerProfile;

        AvailabilityRule::where('id', $ruleId)
            ->where('astrologer_profile_id', $profile->id)
            ->delete();

        \App\Services\AdminActivityLogger::log('astrologer.availability_rule_deleted', $user, ['rule_id' => $ruleId]);

        return back()->with('success', 'Availability slot removed.');
    }

    public function storeAvailabilityException(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:blocked,extra',
            'start_time_utc' => 'nullable|date_format:H:i',
            'end_time_utc' => 'nullable|date_format:H:i',
            'note' => 'nullable|string|max:255',
        ]);

        $user = User::findOrFail($id);
        $profile = $user->astrologerProfile;

        AvailabilityException::create([
            'astrologer_profile_id' => $profile->id,
            'date' => $request->date,
            'type' => $request->type,
            'start_time_utc' => $request->start_time_utc,
            'end_time_utc' => $request->end_time_utc,
            'note' => $request->note,
        ]);

        \App\Services\AdminActivityLogger::log('astrologer.availability_exception_created', $user);

        return back()->with('success', 'Availability exception added.');
    }

    public function deleteAvailabilityException($id, $exceptionId)
    {
        $user = User::findOrFail($id);
        $profile = $user->astrologerProfile;

        AvailabilityException::where('id', $exceptionId)
            ->where('astrologer_profile_id', $profile->id)
            ->delete();

        \App\Services\AdminActivityLogger::log('astrologer.availability_exception_deleted', $user, ['exception_id' => $exceptionId]);

        return back()->with('success', 'Availability exception removed.');
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
