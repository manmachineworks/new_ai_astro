@extends('admin.layouts.app')

@section('title', 'Create Admin')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm rounded-4 border-0">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h4 class="fw-bold mb-0">Create New Admin</h4>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('admin.admin-users.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label fw-bold small text-uppercase text-muted">Full Name</label>
                                <input type="text" name="name" class="form-control" required placeholder="e.g. John Doe">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small text-uppercase text-muted">Email Address</label>
                                <input type="email" name="email" class="form-control" required
                                    placeholder="john@example.com">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small text-uppercase text-muted">Password</label>
                                <input type="password" name="password" class="form-control" required
                                    placeholder="Min 8 characters">
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold small text-uppercase text-muted">Assign Role</label>
                                <select name="role" class="form-select" required>
                                    <option value="">Select a Role...</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text mt-2">
                                    <ul class="mb-0 ps-3 small text-muted">
                                        <li><strong>Super Admin:</strong> Full access to everything.</li>
                                        <li><strong>Finance Admin:</strong> Payments, Wallets, Commissions.</li>
                                        <li><strong>Support Admin:</strong> User management, Refunds, Disputes.</li>
                                        <li><strong>Ops Admin:</strong> Astrologer verification, Content.</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <a href="{{ route('admin.admin-users.index') }}"
                                    class="btn btn-light rounded-pill px-4 me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary rounded-pill px-4">Create Admin</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection