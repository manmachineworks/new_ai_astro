@extends('admin.layouts.app')

@section('title', 'AI Chat History')

@section('content')
    <div class="row mb-4">
        <!-- Stats Cards -->
        <div class="col-md-3">
            <div class="card shadow-sm border-start-primary h-100 rounded-4">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">Total Sessions</div>
                    <h3 class="mb-0 fw-bold">{{ number_format($aggregates['total_sessions']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-start-success h-100 rounded-4">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">Revenue</div>
                    <h3 class="mb-0 text-success fw-bold">₹{{ number_format($aggregates['total_revenue'], 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-start-info h-100 rounded-4">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">Commission</div>
                    <h3 class="mb-0 text-info fw-bold">₹{{ number_format($aggregates['total_commission'], 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-start-warning h-100 rounded-4">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">Total Messages</div>
                    <h3 class="mb-0 fw-bold">{{ number_format($aggregates['total_messages']) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <x-admin.filter-bar :action="route('admin.ai_chats.index')" :filters="['date', 'status']" />

    <x-admin.table :columns="['ID', 'Date', 'User', 'Messages', 'Cost', 'Status', 'Actions']" :rows="$sessions">
        @forelse($sessions as $session)
            <tr>
                <td class="ps-4 font-monospace small text-muted">{{ Str::limit($session->id, 8, '') }}</td>
                <td>
                    <div class="fw-bold text-dark">{{ $session->created_at->format('M d') }}</div>
                    <div class="small text-muted">{{ $session->created_at->format('H:i') }}</div>
                </td>
                <td>
                    @if($session->user)
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle-sm bg-light text-secondary me-2 rounded-circle d-flex align-items-center justify-content-center"
                                style="width:30px;height:30px;">
                                <i class="fas fa-user"></i>
                            </div>
                            <a href="{{ route('admin.users.show', $session->user_id) }}"
                                class="text-decoration-none fw-bold text-dark">{{ $session->user->name }}</a>
                        </div>
                    @else
                        <span class="text-muted fst-italic">Deleted User</span>
                    @endif
                </td>
                <td>
                    <span class="badge bg-light text-dark border">{{ $session->total_messages ?? 0 }}</span>
                </td>
                <td class="text-success fw-bold">₹{{ number_format($session->total_charged, 2) }}</td>
                <td>
                    @php
                        $badgeClass = match ($session->status) {
                            'completed' => 'success-subtle text-success',
                            'active' => 'primary-subtle text-primary',
                            'terminated' => 'secondary-subtle text-secondary',
                            default => 'light text-dark border'
                        };
                    @endphp
                    <span class="badge bg-{{ $badgeClass }} rounded-pill px-3">{{ ucfirst($session->status) }}</span>
                </td>
                <td class="text-end pe-4">
                    <a href="{{ route('admin.ai_chats.show', $session->id) }}" class="btn btn-sm btn-light rounded-circle"
                        data-bs-toggle="tooltip" title="View Transcript">
                        <i class="fas fa-eye text-primary"></i>
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center py-5">
                    <div class="text-muted mb-2"><i class="fas fa-robot fa-3x opacity-25"></i></div>
                    <p class="text-muted">No AI chat sessions found.</p>
                </td>
            </tr>
        @endforelse
    </x-admin.table>
@endsection