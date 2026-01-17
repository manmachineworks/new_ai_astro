@extends('admin.layouts.app')

@section('title', 'Edit Role')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold m-0">Edit Role: {{ $role->name }}</h2>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-light rounded-pill px-4">
            <i class="fas fa-arrow-left me-2"></i>Back
        </a>
    </div>

    <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold">Role Name</label>
                    <input type="text" name="name" class="form-control bg-light border-0 rounded-pill px-3" required value="{{ $role->name }}">
                    @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="fw-bold m-0">Permissions</h5>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    @foreach($permissions as $groupName => $perms)
                        <div class="col-md-4 mb-4">
                            <h6 class="text-uppercase text-muted small fw-bold mb-3 border-bottom pb-2">{{ ucfirst($groupName) }}</h6>
                            @foreach($perms as $perm)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $perm->name }}" id="perm_{{ $perm->id }}"
                                        {{ $role->hasPermissionTo($perm->name) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="perm_{{ $perm->id }}">
                                        {{ $perm->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold">Update Role</button>
    </form>
</div>
@endsection
