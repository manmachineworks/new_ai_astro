<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminActivityLogger;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    private const PROTECTED_ROLES = ['Super Admin'];

    public function index()
    {
        $roles = Role::with('permissions')->orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get();

        return view('admin.roles.index', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions' => ['array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        if (!empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        AdminActivityLogger::log('role.created', $role, ['permissions' => $validated['permissions'] ?? []]);

        return redirect()->route('admin.roles.index')->with('status', 'Role created.');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('name')->get();

        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        if (in_array($role->name, self::PROTECTED_ROLES, true)) {
            return redirect()->route('admin.roles.index')->with('error', 'System roles cannot be modified.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,' . $role->id],
            'permissions' => ['array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role->update(['name' => $validated['name']]);
        $role->syncPermissions($validated['permissions'] ?? []);

        AdminActivityLogger::log('role.updated', $role, ['permissions' => $validated['permissions'] ?? []]);

        return redirect()->route('admin.roles.index')->with('status', 'Role updated.');
    }

    public function destroy(Role $role)
    {
        if (in_array($role->name, self::PROTECTED_ROLES, true)) {
            return redirect()->route('admin.roles.index')->with('error', 'System roles cannot be deleted.');
        }

        $role->delete();
        AdminActivityLogger::log('role.deleted', $role);

        return redirect()->route('admin.roles.index')->with('status', 'Role deleted.');
    }
}
