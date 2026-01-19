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

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="text-muted small">Filters apply to aggregates and list below.</div>
        <a href="{{ route('admin.calls.index', array_merge(request()->query(), ['export' => 'csv'])) }}"
            class="btn btn-outline-secondary btn-sm rounded-pill">
            <i class="fas fa-file-csv me-1"></i>Export CSV
        </a>
    </div>

    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.calls.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                        placeholder="User, phone, astrologer">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        @foreach(['completed','failed','missed','rejected','initiated','active'] as $status)
                            <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Astrologer</label>
                    <select name="astrologer_profile_id" class="form-select">
                        <option value="">All</option>
                        @foreach($astrologers as $astro)
                            <option value="{{ $astro->id }}" @selected(request('astrologer_profile_id') == $astro->id)>
                                {{ $astro->display_name ?? ('Astrologer #' . $astro->id) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                    <a href="{{ route('admin.calls.index') }}" class="btn btn-light w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <x-admin.table :columns="['ID', 'Date', 'User', 'Astrologer', 'Duration', 'Cost', 'Comm.', 'Recording', 'Status', 'Actions']"
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
                    @php $recordingUrl = data_get($call->meta_json, 'recording_url'); @endphp
                    @if(!empty($recordingUrl))
                        <a href="{{ $recordingUrl }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-play"></i>
                        </a>
                    @else
                        <span class="text-muted small">-</span>
                    @endif
                </td>
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
                <td colspan="10" class="text-center py-5">
                    <div class="text-muted mb-2"><i class="fas fa-phone-slash fa-3x opacity-25"></i></div>
                    <p class="text-muted">No calls found.</p>
                </td>
            </tr>
        @endforelse
    </x-admin.table>
@endsection


