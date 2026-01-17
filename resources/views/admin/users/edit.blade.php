@extends('admin.layouts.app')

@section('title', 'Edit User')
@section('page_title', 'Edit User')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control" value="{{ $user->name }}" disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" class="form-control" value="{{ $user->phone }}" disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select">
                        <option value="">Select role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" @selected($user->hasRole($role->name))>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Wallet Balance</label>
                    <input type="number" step="0.01" min="0" name="wallet_balance" class="form-control"
                        value="{{ $user->wallet_balance }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="is_active" class="form-select">
                        <option value="1" @selected($user->is_active)>Active</option>
                        <option value="0" @selected(!$user->is_active)>Blocked</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Admin Notes</label>
                    <textarea name="admin_notes" class="form-control" rows="4"
                        placeholder="Internal notes about this user...">{{ $user->admin_notes }}</textarea>
                </div>
                <button class="btn btn-primary">Save</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Back</a>
            </form>
        </div>
    </div>
@endsection