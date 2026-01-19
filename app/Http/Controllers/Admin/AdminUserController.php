<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSecurityFlag;
use App\Models\AiChatSession;
use App\Models\Appointment;
use App\Services\AdminActivityLogger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles')->orderByDesc('id');

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filters
        if ($request->filled('role')) {
            $query->role($request->input('role'));
        }
        if ($request->filled('status')) {
            $isActive = $request->input('status') === 'active';
            $query->where('is_active', $isActive);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }
        if ($request->filled('wallet_min')) {
            $query->where('wallet_balance', '>=', $request->input('wallet_min'));
        }
        if ($request->filled('wallet_max')) {
            $query->where('wallet_balance', '<=', $request->input('wallet_max'));
        }

        // Export
        if ($request->input('export') === 'csv') {
            return $this->exportCsv($query);
        }

        $users = $query->paginate(20)->withQueryString();
        $roles = Role::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'roles'));
    }

    private function exportCsv($query)
    {
        $filename = 'users_export_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($query) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Name', 'Email', 'Phone', 'Role', 'Wallet Balance', 'Joined At', 'Status']);

            $query->chunk(100, function ($users) use ($file) {
                foreach ($users as $user) {
                    fputcsv($file, [
                        $user->id,
                        $user->name,
                        $user->email,
                        $user->phone,
                        $user->roles->pluck('name')->implode(', '),
                        $user->wallet_balance,
                        $user->created_at->format('Y-m-d H:i:s'),
                        $user->is_active ? 'Active' : 'Inactive'
                    ]);
                }
            });
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function show(User $user)
    {
        $user->load(['roles', 'preferences', 'activeMembership']);

        // Load recent activity (relationships should be defined in User model)
        // Since we noticed they might be missing, we'll try to load them if they exist or query explicitly
        $walletTransactions = \App\Models\WalletTransaction::where('user_id', $user->id)->latest()->limit(10)->get();
        $callSessions = \App\Models\CallSession::where('user_id', $user->id)->latest()->limit(10)->get();

        // For chats, checking if ChatSession exists, otherwise fallback or empty
        $chatSessions = class_exists(\App\Models\ChatSession::class)
            ? \App\Models\ChatSession::where('user_id', $user->id)->latest()->limit(10)->get()
            : collect([]);

        $aiSessions = AiChatSession::where('user_id', $user->id)->latest()->limit(10)->get();
        $appointments = Appointment::where('user_id', $user->id)->latest()->limit(10)->get();

        $aiRestriction = UserSecurityFlag::where('user_id', $user->id)
            ->where('flag_type', 'ai_chat_blocked')
            ->where(function ($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->latest()
            ->first();

        return view('admin.users.show', compact(
            'user',
            'walletTransactions',
            'callSessions',
            'chatSessions',
            'aiSessions',
            'appointments',
            'aiRestriction'
        ));
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $validated = $request->validate([
            'role' => ['nullable', 'string', 'exists:roles,name'],
            'is_active' => ['required', 'boolean'],
            'wallet_balance' => ['nullable', 'numeric', 'min:0'],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if (!empty($validated['role'])) {
            $user->syncRoles([$validated['role']]);
        }

        $user->update([
            'is_active' => (bool) $validated['is_active'],
            'wallet_balance' => $validated['wallet_balance'] ?? $user->wallet_balance,
            'admin_notes' => $validated['admin_notes'] ?? $user->admin_notes,
        ]);

        AdminActivityLogger::log('user.updated', $user, [
            'role' => $validated['role'] ?? null,
            'is_active' => $validated['is_active'],
            'wallet_balance' => $validated['wallet_balance'] ?? null,
            'admin_notes_changed' => isset($validated['admin_notes']),
        ]);

        return redirect()->route('admin.users.index')->with('status', 'User updated.');
    }

    public function toggle(User $user)
    {
        $this->authorize('block', $user);

        $user->update(['is_active' => !$user->is_active]);
        AdminActivityLogger::log('user.toggled', $user, ['is_active' => $user->is_active]);

        return redirect()->route('admin.users.index')->with('status', 'User status updated.');
    }

    public function block(Request $request, User $user)
    {
        $this->authorize('block', $user);

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
            'duration_minutes' => 'nullable|integer|min:1',
            'blocked_until' => 'nullable|date',
        ]);

        if (empty($validated['duration_minutes']) && empty($validated['blocked_until'])) {
            return back()->with('error', 'Provide duration or blocked_until.');
        }

        $blockedUntil = $validated['blocked_until']
            ? Carbon::parse($validated['blocked_until'])
            : now()->addMinutes((int) $validated['duration_minutes']);

        $before = $user->only(['blocked_until', 'blocked_reason', 'is_active']);

        $user->update([
            'is_active' => false,
            'blocked_at' => now(),
            'blocked_until' => $blockedUntil,
            'blocked_reason' => $validated['reason'],
            'blocked_by_admin_id' => auth()->id(),
            'unblocked_at' => null,
        ]);

        AdminActivityLogger::log('user.blocked', $user, [
            'reason' => $validated['reason'],
            'blocked_until' => $blockedUntil?->toIso8601String(),
            'before' => $before,
        ]);

        return back()->with('success', 'User blocked.');
    }

    public function unblock(Request $request, User $user)
    {
        $this->authorize('block', $user);

        $before = $user->only(['blocked_until', 'blocked_reason', 'is_active']);

        $user->update([
            'is_active' => true,
            'blocked_until' => null,
            'blocked_reason' => null,
            'blocked_by_admin_id' => null,
            'unblocked_at' => now(),
        ]);

        AdminActivityLogger::log('user.unblocked', $user, [
            'before' => $before,
        ]);

        return back()->with('success', 'User unblocked.');
    }

    public function restrictAiChat(Request $request, User $user)
    {
        $this->authorize('block', $user);

        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
            'duration_minutes' => 'nullable|integer|min:1',
        ]);

        $expiresAt = null;
        if (!empty($validated['duration_minutes'])) {
            $expiresAt = now()->addMinutes((int) $validated['duration_minutes']);
        }

        UserSecurityFlag::create([
            'user_id' => $user->id,
            'flag_type' => 'ai_chat_blocked',
            'expires_at' => $expiresAt,
            'meta_json' => [
                'reason' => $validated['reason'] ?? null,
                'admin_id' => auth()->id(),
            ],
        ]);

        AdminActivityLogger::log('user.ai_chat_blocked', $user, [
            'reason' => $validated['reason'] ?? null,
            'expires_at' => $expiresAt?->toIso8601String(),
        ]);

        return back()->with('success', 'AI chat restricted for user.');
    }

    public function liftAiChatRestriction(Request $request, User $user)
    {
        $this->authorize('block', $user);

        UserSecurityFlag::where('user_id', $user->id)
            ->where('flag_type', 'ai_chat_blocked')
            ->where(function ($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->update(['expires_at' => now()]);

        AdminActivityLogger::log('user.ai_chat_unblocked', $user);

        return back()->with('success', 'AI chat restriction lifted.');
    }
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id',
            'action' => 'required|in:activate,deactivate,delete,export',
        ], [
            'ids.required' => 'Please select at least one user.',
            'action.required' => 'Please select an action.',
        ]);

        $ids = $request->ids;
        $action = $request->action;

        if ($action === 'export') {
            return $this->exportUsers($ids);
        }

        $users = User::whereIn('id', $ids)->get();
        $count = 0;

        foreach ($users as $user) {
            // Prevent self-action if admin is in the list
            if ($user->id === auth()->id())
                continue;

            switch ($action) {
                case 'activate':
                    if (!$user->is_active) {
                        $user->update(['is_active' => true]);
                        AdminActivityLogger::log('bulk_activate_user', $user);
                        $count++;
                    }
                    break;
                case 'deactivate':
                    if ($user->is_active) {
                        $user->update(['is_active' => false]);
                        AdminActivityLogger::log('bulk_deactivate_user', $user);
                        $count++;
                    }
                    break;
                case 'delete':
                    // Implement safe delete or soft delete
                    // $user->delete();
                    // $count++;
                    break;
            }
        }

        return back()->with('success', "Bulk action '{$action}' applied to {$count} users.");
    }

    private function exportUsers($ids)
    {
        $filename = 'users_bulk_export_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($ids) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Name', 'Email', 'Phone', 'Role', 'Status', 'Wallet Balance', 'Joined']);

            User::whereIn('id', $ids)->with('roles')->chunk(100, function ($users) use ($file) {
                foreach ($users as $user) {
                    fputcsv($file, [
                        $user->id,
                        $user->name,
                        $user->email,
                        $user->phone,
                        $user->roles->pluck('name')->implode(', '),
                        $user->is_active ? 'Active' : 'Inactive',
                        $user->wallet_balance,
                        $user->created_at->format('Y-m-d H:i:s')
                    ]);
                }
            });
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
