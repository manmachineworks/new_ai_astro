@extends('admin.layouts.app')

@section('title', 'Support Tickets')
@section('page_title', 'Support Tickets')

@section('content')
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Open Tickets</h6>
                    <h3 class="mb-0 text-warning">{{ $stats['open'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Pending Response</h6>
                    <h3 class="mb-0 text-danger">{{ $stats['pending'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Resolved Today</h6>
                    <h3 class="mb-0 text-success">{{ $stats['resolved_today'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Avg Response Time</h6>
                    <h3 class="mb-0">{{ $stats['avg_response_hours'] }}h</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link {{ request('status', 'open') == 'open' ? 'active' : '' }}" href="?status=open">
                        Open <span class="badge bg-warning">{{ $stats['open'] }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('status') == 'pending' ? 'active' : '' }}" href="?status=pending">
                        Pending
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('status') == 'resolved' ? 'active' : '' }}" href="?status=resolved">
                        Resolved
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('status') == 'closed' ? 'active' : '' }}" href="?status=closed">
                        Closed
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <form method="GET" class="row g-2">
                    <input type="hidden" name="status" value="{{ request('status') }}">
                    <div class="col-md-3">
                        <select name="category" class="form-select form-select-sm">
                            <option value="">All Categories</option>
                            <option value="payment" {{ request('category') == 'payment' ? 'selected' : '' }}>Payment</option>
                            <option value="call" {{ request('category') == 'call' ? 'selected' : '' }}>Call</option>
                            <option value="chat" {{ request('category') == 'chat' ? 'selected' : '' }}>Chat</option>
                            <option value="account" {{ request('category') == 'account' ? 'selected' : '' }}>Account</option>
                            <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="priority" class="form-select form-select-sm">
                            <option value="">All Priorities</option>
                            <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                            <option value="normal" {{ request('priority') == 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-sm btn-outline-primary">Filter</button>
                        <a href="{{ route('admin.support.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Subject</th>
                            <th>Category</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                            <tr class="{{ $ticket->priority == 'high' ? 'table-danger' : '' }}">
                                <td><code>#{{ $ticket->id }}</code></td>
                                <td>
                                    <div>{{ $ticket->user->name }}</div>
                                    <small class="text-muted">{{ $ticket->user->phone }}</small>
                                </td>
                                <td>{{ Str::limit($ticket->subject, 50) }}</td>
                                <td><span class="badge bg-light text-dark">{{ ucfirst($ticket->category) }}</span></td>
                                <td>
                                    @if($ticket->priority == 'high')
                                        <span class="badge bg-danger">High</span>
                                    @elseif($ticket->priority == 'normal')
                                        <span class="badge bg-primary">Normal</span>
                                    @else
                                        <span class="badge bg-secondary">Low</span>
                                    @endif
                                </td>
                                <td>
                                    @if($ticket->status == 'open')
                                        <span class="badge bg-warning">Open</span>
                                    @elseif($ticket->status == 'pending')
                                        <span class="badge bg-info">Pending</span>
                                    @elseif($ticket->status == 'resolved')
                                        <span class="badge bg-success">Resolved</span>
                                    @else
                                        <span class="badge bg-secondary">Closed</span>
                                    @endif
                                </td>
                                <td>
                                    <div>{{ $ticket->created_at->format('d M Y') }}</div>
                                    <small class="text-muted">{{ $ticket->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <a href="{{ route('admin.support.show', $ticket) }}" class="btn btn-sm btn-primary">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No tickets found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $tickets->links() }}
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
@endpush