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

    <x-admin.table :columns="['Name', 'Email', 'Role', 'Joined', 'Actions']" :rows="$admins">
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
                <td class="small text-muted">
                    {{ $admin->created_at->format('M d, Y') }}
                </td>
                <td class="text-end pe-4">
                    <a href="{{ route('admin.admin-users.edit', $admin->id) }}"
                        class="btn btn-sm btn-light rounded-circle text-primary me-2" data-bs-toggle="tooltip" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    @if($admin->id != auth()->id())
                        <form action="{{ route('admin.admin-users.destroy', $admin->id) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Are you sure you want to remove this admin?');">
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