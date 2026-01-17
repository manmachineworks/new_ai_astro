@extends('admin.layouts.app')

@section('title', 'System Audit Logs')

@section('content')
<div class="row mb-4">
    <!-- Stats Cards -->
     <div class="col-md-3">
        <div class="card shadow-sm border-start-primary h-100 rounded-4">
            <div class="card-body">
                <div class="text-muted small text-uppercase">Total Actions</div>
                <h3 class="mb-0 fw-bold">{{ number_format($logs->total()) }}</h3>
            </div>
        </div>
    </div>
</div>

<x-admin.filter-bar :action="route('admin.system.audit_logs.index')" :filters="['date', 'search']">
    <div class="col-md-3">
        <select name="admin_id" class="form-select bg-light border-0">
            <option value="">All Admins</option>
            @foreach($admins as $admin)
                <option value="{{ $admin->id }}" {{ request('admin_id') == $admin->id ? 'selected' : '' }}>
                    {{ $admin->name }}
                </option>
            @endforeach
        </select>
    </div>
</x-admin.filter-bar>

<x-admin.table :columns="['Date', 'Admin', 'Action', 'Target', 'Metadata', 'IP/Agent', 'Actions']" :rows="$logs">
    @forelse($logs as $log)
        <tr>
             <td class="ps-4">
                 <div class="fw-bold text-dark">{{ $log->created_at->format('M d, Y') }}</div>
                 <div class="small text-muted">{{ $log->created_at->format('H:i:s') }}</div>
            </td>
             <td>
                @if($log->causer)
                    <span class="fw-bold text-dark">{{ $log->causer->name }}</span>
                @else
                    <span class="text-muted fst-italic">System / Deleted</span>
                @endif
            </td>
             <td>
                <span class="badge bg-light text-primary border border-primary-subtle text-uppercase">{{ $log->action }}</span>
            </td>
             <td>
                 @if($log->target_type)
                    <div class="small font-monospace text-muted">{{ class_basename($log->target_type) }}</div>
                    <div class="small fw-bold">#{{ $log->target_id }}</div>
                 @else
                    <span class="text-muted">-</span>
                 @endif
            </td>
             <td class="small text-muted">
                 {{ Str::limit(json_encode($log->metadata), 40) }}
            </td>
             <td class="small text-muted">
                <div>{{ $log->ip_address }}</div>
                <div class="text-truncate" style="max-width: 150px;" title="{{ $log->user_agent }}">{{ $log->user_agent }}</div>
            </td>
             <td class="text-end pe-4">
                 <a href="{{ route('admin.system.audit_logs.show', $log->id) }}" class="btn btn-sm btn-light rounded-circle text-primary" data-bs-toggle="tooltip" title="View Detail">
                    <i class="fas fa-eye"></i>
                </a>
            </td>
        </tr>
    @empty
        <tr>
             <td colspan="7" class="text-center py-5">
                 <div class="text-muted mb-2"><i class="fas fa-clipboard-list fa-3x opacity-25"></i></div>
                <p class="text-muted">No audit logs found.</p>
            </td>
        </tr>
    @endforelse
</x-admin.table>
@endsection