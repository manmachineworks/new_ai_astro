<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AdminActivityLogger;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->orderByDesc('id')->paginate(20);
        $roles = Role::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'roles'));
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
        ]);

        if (!empty($validated['role'])) {
            $user->syncRoles([$validated['role']]);
        }

        $user->update([
            'is_active' => (bool) $validated['is_active'],
            'wallet_balance' => $validated['wallet_balance'] ?? $user->wallet_balance,
        ]);

        AdminActivityLogger::log('user.updated', $user, [
            'role' => $validated['role'] ?? null,
            'is_active' => $validated['is_active'],
            'wallet_balance' => $validated['wallet_balance'] ?? null,
        ]);

        return redirect()->route('admin.users.index')->with('status', 'User updated.');
    }

    public function toggle(User $user)
    {
        $this->authorize('block', $user);

        $user->update(['is_active' => ! $user->is_active]);
        AdminActivityLogger::log('user.toggled', $user, ['is_active' => $user->is_active]);

        return redirect()->route('admin.users.index')->with('status', 'User status updated.');
    }
}
