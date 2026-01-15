@extends('admin.layouts.app')

@section('title', 'Users')
@section('page_title', 'User Management')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-sm align-middle">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Wallet</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->name ?? '-' }}</td>
                            <td>{{ $user->phone }}</td>
                            <td>{{ $user->roles->pluck('name')->implode(', ') ?: 'User' }}</td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge text-bg-success">Active</span>
                                @else
                                    <span class="badge text-bg-danger">Blocked</span>
                                @endif
                            </td>
                            <td>{{ number_format($user->wallet_balance, 2) }}</td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.users.edit', $user) }}">Edit</a>
                                @can('block_users')
                                    <form method="POST" action="{{ route('admin.users.toggle', $user) }}" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-sm btn-outline-warning" onclick="return confirm('Toggle user status?')">
                                            Toggle
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $users->links() }}
        </div>
    </div>
@endsection
