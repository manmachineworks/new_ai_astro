@extends('admin.layouts.app')

@section('title', 'Call History')

@section('content')
    <div class="row mb-4">
        <!-- Stats Cards -->
        <div class="col-md-3">
            <div class="card shadow-sm border-start-primary h-100 rounded-4">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">Total Calls</div>
                    <h3 class="mb-0 fw-bold">{{ number_format($aggregates['total_calls']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm border-start-success h-100 rounded-4">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">Revenue</div>
                    <h3 class="mb-0 text-success fw-bold">₹{{ number_format($aggregates['total_revenue'], 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm border-start-info h-100 rounded-4">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">Commission</div>
                    <h3 class="mb-0 text-info fw-bold">₹{{ number_format($aggregates['total_commission'], 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm border-start-warning h-100 rounded-4">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">Minutes</div>
                    <h3 class="mb-0 fw-bold">{{ number_format($aggregates['total_minutes']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-start-secondary h-100 rounded-4">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">Astro Earnings</div>
                    <h3 class="mb-0 text-secondary fw-bold">₹{{ number_format($aggregates['total_astrologer_earning'], 2) }}
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <x-admin.filter-bar :action="route('admin.calls.index')" :filters="['date', 'status']" />

    <x-admin.table :columns="['ID', 'Date', 'User', 'Astrologer', 'Duration', 'Cost', 'Comm.', 'Status', 'Actions']"
        :rows="$calls">
        @forelse($calls as $call)
            <tr>
                <td class="ps-4 font-monospace small text-muted">{{ Str::limit($call->id, 8, '') }}</td>
                <td>
                    <div class="fw-bold text-dark">{{ $call->created_at->format('M d') }}</div>
                    <div class="small text-muted">{{ $call->created_at->format('H:i') }}</div>
                </td>
                <td>
                    @if($call->user)
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle-sm bg-light text-secondary me-2 rounded-circle d-flex align-items-center justify-content-center"
                                style="width:30px;height:30px;">
                                <i class="fas fa-user"></i>
                            </div>
                            <a href="{{ route('admin.users.show', $call->user_id) }}"
                                class="text-decoration-none fw-bold text-dark">{{ $call->user->name }}</a>
                        </div>
                    @else
                        <span class="text-muted fst-italic">Deleted User</span>
                    @endif
                </td>
                <td>
                    @if($call->astrologerProfile)
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle-sm bg-light text-secondary me-2 rounded-circle d-flex align-items-center justify-content-center"
                                style="width:30px;height:30px;">
                                <i class="fas fa-star"></i>
                            </div>
                            <a href="{{ route('admin.astrologers.show', $call->astrologerProfile->user_id) }}"
                                class="text-decoration-none fw-bold text-dark">
                                {{ $call->astrologerProfile->display_name }}
                            </a>
                        </div>
                    @else
                        <span class="text-muted fst-italic">Deleted</span>
                    @endif
                </td>
                <td>
                    <div class="fw-bold">{{ $call->duration_seconds > 0 ? gmdate('H:i:s', $call->duration_seconds) : '-' }}
                    </div>
                    @if($call->billable_minutes > 0)
                        <div class="small text-muted">{{ $call->billable_minutes }} mins</div>
                    @endif
                </td>
                <td class="text-success fw-bold">₹{{ number_format($call->gross_amount, 2) }}</td>
                <td class="text-info small">₹{{ number_format($call->platform_commission_amount, 2) }}</td>
                <td>
                    @php
                        $badgeClass = match ($call->status) {
                            'completed' => 'success-subtle text-success',
                            'failed' => 'danger-subtle text-danger',
                            'missed' => 'warning-subtle text-warning',
                            'rejected' => 'secondary-subtle text-secondary',
                            default => 'light text-dark border'
                        };
                    @endphp
                    <span class="badge bg-{{ $badgeClass }} rounded-pill px-3">{{ ucfirst($call->status) }}</span>
                </td>
                <td class="text-end pe-4">
                    <a href="{{ route('admin.calls.show', $call->id) }}" class="btn btn-sm btn-light rounded-circle"
                        data-bs-toggle="tooltip" title="View Details">
                        <i class="fas fa-eye text-primary"></i>
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="text-center py-5">
                    <div class="text-muted mb-2"><i class="fas fa-phone-slash fa-3x opacity-25"></i></div>
                    <p class="text-muted">No calls found.</p>
                </td>
            </tr>
        @endforelse
    </x-admin.table>
@endsection