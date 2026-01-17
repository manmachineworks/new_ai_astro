@extends('admin.layouts.app')

@section('title', 'Appointments')
@section('page_title', 'Appointments')

@section('content')
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.appointments.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        @foreach(['requested','confirmed','declined','cancelled_by_user','cancelled_by_astrologer','completed','expired','no_show'] as $status)
                            <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Astrologer</label>
                    <select name="astrologer_id" class="form-select">
                        <option value="">All</option>
                        @foreach($astrologers as $astrologer)
                            <option value="{{ $astrologer->id }}" {{ request('astrologer_id') == $astrologer->id ? 'selected' : '' }}>
                                {{ $astrologer->display_name ?? 'Astrologer #' . $astrologer->id }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Start Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">End Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>ID / Date</th>
                        <th>User</th>
                        <th>Astrologer</th>
                        <th>Time (UTC)</th>
                        <th>Status</th>
                        <th>Amount</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $appointment)
                        <tr>
                            <td>
                                <div class="fw-bold">#{{ substr($appointment->id, 0, 8) }}</div>
                                <small class="text-muted">{{ $appointment->created_at->format('M d, h:i A') }}</small>
                            </td>
                            <td>
                                <div>{{ $appointment->user?->name ?? 'User' }}</div>
                                <small class="text-muted">{{ $appointment->user?->phone ?? '-' }}</small>
                            </td>
                            <td>
                                <div>{{ $appointment->astrologerProfile?->display_name ?? 'Astrologer' }}</div>
                                <small class="text-muted">{{ $appointment->astrologerProfile?->user?->phone ?? '-' }}</small>
                            </td>
                            <td>
                                {{ $appointment->start_at_utc->format('M d, h:i A') }}
                            </td>
                            <td>
                                <span class="badge rounded-pill
                                    @if($appointment->status === 'confirmed') bg-success
                                    @elseif($appointment->status === 'requested') bg-warning text-dark
                                    @elseif(str_contains($appointment->status, 'cancelled')) bg-danger
                                    @else bg-secondary @endif">
                                    {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                                </span>
                            </td>
                            <td>ƒ,1{{ number_format($appointment->price_total, 2) }}</td>
                            <td>
                                <a href="{{ route('admin.appointments.show', $appointment->id) }}"
                                    class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">No appointments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">
            {{ $appointments->links() }}
        </div>
    </div>
@endsection
