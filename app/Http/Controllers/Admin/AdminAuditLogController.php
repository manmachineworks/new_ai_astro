<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class AdminAuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AdminActivityLog::with('causer')->latest();

        // Search Action or Target ID
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                    ->orWhere('target_id', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by Admin User
        if ($request->filled('admin_id')) {
            $query->where('causer_id', $request->admin_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        $logs = $query->paginate(50)->withQueryString();

        // Fetch list of admins for filter
        $admins = User::role('Admin')->orderBy('name')->get(['id', 'name']);

        return view('admin.audit_logs.index', compact('logs', 'admins'));
    }

    public function show($id)
    {
        $log = AdminActivityLog::with('causer')->findOrFail($id);
        return view('admin.audit_logs.show', compact('log'));
    }
}
