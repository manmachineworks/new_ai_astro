@extends('admin.layouts.app')

@section('title', 'Manage Admins')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold m-0 text-dark">Admin Users</h2>
            <p class="text-muted mb-0">Manage system administrators and their roles.</p>
        </div>
        <a href="{{ route('admin.admin-users.create') }}" class="btn btn-primary rounded-pill px-4">
            <i class="fas fa-plus-circle me-2"></i> Create New Admin
        </a>
    </div>

    <x-admin.table :columns="['Name', 'Email', 'Role', 'Status', 'Last Login', 'Joined', 'Actions']" :rows="$admins">
        @forelse($admins as $admin)
            <tr>
                <td class="ps-4 fw-bold text-dark">
                    {{ $admin->name }}
                    @if($admin->id == auth()->id())
                        <span class="badge bg-success-subtle text-success ms-2">You</span>
                    @endif
                </td>
                <td class="text-muted">
                    {{ $admin->email }}
                </td>
                <td>
                    @foreach($admin->roles as $role)
                        <span
                            class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3">{{ $role->name }}</span>
                    @endforeach
                </td>
                <td>
                    @if($admin->is_active)
                        <span class="badge bg-success-subtle text-success rounded-pill px-3">Active</span>
                    @else
                        <span class="badge bg-danger-subtle text-danger rounded-pill px-3">Disabled</span>
                    @endif
                </td>
                <td class="small text-muted">
                    {{ $admin->last_login_at ? $admin->last_login_at->format('M d, Y H:i') : '—' }}
                </td>
                <td class="small text-muted">
                    {{ $admin->created_at->format('M d, Y') }}
                </td>
                <td class="text-end pe-4">
                    <a href="{{ route('admin.admin-users.edit', $admin->id) }}"
                        class="btn btn-sm btn-light rounded-circle text-primary me-2" data-bs-toggle="tooltip" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    @if($admin->id != auth()->id())
                        <form action="{{ route('admin.admin-users.toggle', $admin->id) }}" method="POST" class="d-inline"
                            data-confirm data-confirm-title="Toggle Admin Status"
                            data-confirm-text="Are you sure you want to toggle this admin's access?">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-light rounded-circle text-warning me-2"
                                data-bs-toggle="tooltip" title="Toggle Status">
                                <i class="fas fa-user-lock"></i>
                            </button>
                        </form>
                        <form action="{{ route('admin.admin-users.force-logout', $admin->id) }}" method="POST" class="d-inline"
                            data-confirm data-confirm-title="Force Logout"
                            data-confirm-text="Force this admin to log out from all sessions?">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-light rounded-circle text-secondary me-2"
                                data-bs-toggle="tooltip" title="Force Logout">
                                <i class="fas fa-door-open"></i>
                            </button>
                        </form>
                    @endif
                    @if($admin->id != auth()->id())
                        <form action="{{ route('admin.admin-users.destroy', $admin->id) }}" method="POST" class="d-inline"
                            data-confirm data-confirm-title="Remove Admin"
                            data-confirm-text="Remove this admin account permanently?">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-light rounded-circle text-danger" data-bs-toggle="tooltip"
                                title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center py-5">
                    <div class="text-muted mb-2"><i class="fas fa-users-cog fa-3x opacity-25"></i></div>
                    <p class="text-muted">No admin users found.</p>
                </td>
            </tr>
        @endforelse
    </x-admin.table>
@endsection
