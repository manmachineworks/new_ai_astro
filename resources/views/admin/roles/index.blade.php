@extends('admin.layouts.app')

@section('title', 'Role Management')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold m-0 text-dark">Roles & Permissions</h2>
            <p class="text-muted mb-0">Manage system access and security roles.</p>
        </div>
        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary rounded-pill px-4">
            <i class="fas fa-plus-circle me-2"></i> Create New Role
        </a>
    </div>

    <x-admin.table :columns="['Role Name', 'Unsanitized Name', 'Permissions', 'Actions']" :rows="$roles">
        @forelse($roles as $role)
            <tr>
                <td class="ps-4 fw-bold text-dark text-capitalize">
                    {{ $role->name }}
                </td>
                <td class="small font-monospace text-muted">
                    {{ $role->name }}
                </td>
                <td>
                    @foreach($role->permissions->take(5) as $perm)
                        <span class="badge bg-light text-secondary border me-1">{{ $perm->name }}</span>
                    @endforeach
                    @if($role->permissions->count() > 5)
                        <span class="badge bg-light text-muted border">+{{ $role->permissions->count() - 5 }} more</span>
                    @endif
                </td>
                <td class="text-end pe-4">
                    @if($role->name !== 'Super Admin')
                        <a href="{{ route('admin.roles.edit', $role->id) }}"
                            class="btn btn-sm btn-light rounded-circle text-primary me-2" data-bs-toggle="tooltip"
                            title="Edit Role">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Are you sure? This will remove this role from all users.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-light rounded-circle text-danger" data-bs-toggle="tooltip"
                                title="Delete Role">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    @else
                        <span class="text-muted small fst-italic me-3">System Role</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center py-5">
                    <div class="text-muted mb-2"><i class="fas fa-user-shield fa-3x opacity-25"></i></div>
                    <p class="text-muted">No roles defined.</p>
                </td>
            </tr>
        @endforelse
    </x-admin.table>
@endsection