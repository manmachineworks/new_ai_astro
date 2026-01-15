<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminActivityLogger;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::orderBy('name')->get();

        return view('admin.permissions.index', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name'],
        ]);

        $permission = Permission::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        AdminActivityLogger::log('permission.created', $permission);

        return redirect()->route('admin.permissions.index')->with('status', 'Permission created.');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        AdminActivityLogger::log('permission.deleted', $permission);

        return redirect()->route('admin.permissions.index')->with('status', 'Permission deleted.');
    }
}
