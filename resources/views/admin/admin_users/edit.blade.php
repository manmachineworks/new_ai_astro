@extends('admin.layouts.app')

@section('title', 'Edit Admin')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm rounded-4 border-0">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h4 class="fw-bold mb-0">Edit Admin: {{ $admin->name }}</h4>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('admin.admin-users.update', $admin->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label fw-bold small text-uppercase text-muted">Full Name</label>
                                <input type="text" name="name" class="form-control" value="{{ $admin->name }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small text-uppercase text-muted">Email Address</label>
                                <input type="email" name="email" class="form-control" value="{{ $admin->email }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small text-uppercase text-muted">Password
                                    (Optional)</label>
                                <input type="password" name="password" class="form-control"
                                    placeholder="Leave blank to keep current">
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold small text-uppercase text-muted">Assign Role</label>
                                <select name="role" class="form-select" required>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" {{ $admin->hasRole($role->name) ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="d-flex justify-content-end">
                                <a href="{{ route('admin.admin-users.index') }}"
                                    class="btn btn-light rounded-pill px-4 me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary rounded-pill px-4">Update Admin</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection