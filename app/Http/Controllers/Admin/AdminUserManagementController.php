<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Services\AdminActivityLogger;

class AdminUserManagementController extends Controller
{
    public function index(Request $request)
    {
        // Fetch users who have any Admin-like role
        $query = User::role(['Super Admin', 'Admin', 'Finance Admin', 'Support Admin', 'Ops Admin']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $admins = $query->paginate(20)->withQueryString();

        return view('admin.admin_users.index', compact('admins'));
    }

    public function create()
    {
        $roles = Role::whereIn('name', ['Super Admin', 'Finance Admin', 'Support Admin', 'Ops Admin'])->get();
        return view('admin.admin_users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role' => 'required|exists:roles,name'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(), // Auto-verify admins
        ]);

        $user->assignRole($request->role);

        AdminActivityLogger::log('create_admin', $user, ['role' => $request->role]);

        return redirect()->route('admin.admin-users.index')->with('success', 'Admin created successfully.');
    }

    public function edit($id)
    {
        $admin = User::findOrFail($id);
        $roles = Role::whereIn('name', ['Super Admin', 'Finance Admin', 'Support Admin', 'Ops Admin'])->get();
        return view('admin.admin_users.edit', compact('admin', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $admin = User::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|exists:roles,name'
        ]);

        $admin->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8']);
            $admin->update(['password' => Hash::make($request->password)]);
        }

        $admin->syncRoles([$request->role]);

        AdminActivityLogger::log('update_admin', $admin, ['role' => $request->role]);

        return redirect()->route('admin.admin-users.index')->with('success', 'Admin updated successfully.');
    }

    public function destroy($id)
    {
        if ($id == auth()->id()) {
            return back()->with('error', 'You cannot delete yourself.');
        }

        $admin = User::findOrFail($id);
        $admin->roles()->detach();
        $admin->delete();

        AdminActivityLogger::log('delete_admin', $admin);

        return redirect()->route('admin.admin-users.index')->with('success', 'Admin removed successfully.');
    }
}
