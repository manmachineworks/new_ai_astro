<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Services\AdminActivityLogger;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AdminUserManagementController extends Controller
{
    private const ADMIN_ROLE_NAMES = [
        'Super Admin',
        'Admin',
        'Finance Admin',
        'Support Admin',
        'Ops Admin',
        'Moderator',
    ];

    public function index(Request $request)
    {
        // Fetch users who have any Admin-like role (guard against missing roles)
        $existingRoles = Role::whereIn('name', self::ADMIN_ROLE_NAMES)->pluck('name')->all();
        $query = User::query();
        if (!empty($existingRoles)) {
            $query->role($existingRoles);
        } else {
            $query->whereRaw('1 = 0');
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $admins = $query->paginate(20)->withQueryString();

        $lastLogins = AdminActivityLog::select('causer_id', DB::raw('MAX(created_at) as last_login_at'))
            ->where('action', 'admin.login')
            ->whereIn('causer_id', $admins->pluck('id'))
            ->groupBy('causer_id')
            ->pluck('last_login_at', 'causer_id');

        $admins->getCollection()->each(function ($admin) use ($lastLogins) {
            $admin->last_login_at = isset($lastLogins[$admin->id]) ? Carbon::parse($lastLogins[$admin->id]) : null;
        });

        return view('admin.admin_users.index', compact('admins'));
    }

    public function create()
    {
        $roles = Role::whereIn('name', self::ADMIN_ROLE_NAMES)->get();
        return view('admin.admin_users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role' => 'required|exists:roles,name',
            'is_active' => 'required|boolean',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(), // Auto-verify admins
            'is_active' => (bool) $request->is_active,
        ]);

        $user->assignRole($request->role);

        AdminActivityLogger::log('create_admin', $user, ['role' => $request->role]);

        return redirect()->route('admin.admin-users.index')->with('success', 'Admin created successfully.');
    }

    public function edit($id)
    {
        $admin = User::findOrFail($id);
        $roles = Role::whereIn('name', self::ADMIN_ROLE_NAMES)->get();
        return view('admin.admin_users.edit', compact('admin', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $admin = User::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|exists:roles,name',
            'is_active' => 'required|boolean',
        ]);

        $admin->update([
            'name' => $request->name,
            'email' => $request->email,
            'is_active' => (bool) $request->is_active,
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8']);
            $admin->update(['password' => Hash::make($request->password)]);
        }

        $admin->syncRoles([$request->role]);

        AdminActivityLogger::log('update_admin', $admin, ['role' => $request->role]);

        return redirect()->route('admin.admin-users.index')->with('success', 'Admin updated successfully.');
    }

    public function toggleStatus($id)
    {
        $admin = User::findOrFail($id);

        if ($admin->id === auth()->id()) {
            return back()->with('error', 'You cannot disable your own account.');
        }

        $admin->update(['is_active' => !$admin->is_active]);

        AdminActivityLogger::log('admin.status_toggled', $admin, ['is_active' => $admin->is_active]);

        return back()->with('success', 'Admin status updated.');
    }

    public function forceLogout($id)
    {
        $admin = User::findOrFail($id);

        if ($admin->id === auth()->id()) {
            return back()->with('error', 'You cannot force logout your own account.');
        }

        $admin->forceFill(['remember_token' => Str::random(60)])->save();

        if (config('session.driver') === 'database' && Schema::hasTable('sessions')) {
            DB::table('sessions')->where('user_id', $admin->id)->delete();
        }

        AdminActivityLogger::log('admin.force_logout', $admin);

        return back()->with('success', 'Admin session revoked.');
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
