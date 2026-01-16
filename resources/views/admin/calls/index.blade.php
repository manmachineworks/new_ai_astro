@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4 mb-0">Call Audit Logs</h2>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.calls.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed
                            </option>
                            <option value="missed" {{ request('status') == 'missed' ? 'selected' : '' }}>Missed</option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>ID / Date</th>
                            <th>User</th>
                            <th>Astrologer</th>
                            <th>Duration / Cost</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($calls as $call)
                            <tr>
                                <td>
                                    <div class="fw-bold">#{{ substr($call->id, 0, 8) }}</div>
                                    <small class="text-muted">{{ $call->created_at->format('M d, h:i A') }}</small>
                                </td>
                                <td>
                                    <div>{{ $call->user->name ?? 'Guest' }}</div>
                                    <small class="text-muted">{{ $call->user->phone ?? '-' }}</small>
                                </td>
                                <td>
                                    <div>{{ $call->astrologerProfile->display_name }}</div>
                                    <small class="text-muted">{{ $call->astrologerProfile->user->phone ?? '-' }}</small>
                                </td>
                                <td>
                                    @if($call->status === 'completed')
                                        {{ floor($call->duration_seconds / 60) }}m (₹{{ number_format($call->gross_amount, 2) }})
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <span class="badge rounded-pill 
                                        @if($call->status === 'completed') bg-success 
                                        @elseif($call->status === 'failed' || $call->status === 'missed') bg-danger 
                                        @else bg-warning @endif">
                                        {{ ucfirst($call->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.calls.show', $call->id) }}"
                                        class="btn btn-sm btn-outline-primary">View Audit</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">No call logs found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white">
                {{ $calls->links() }}
            </div>
        </div>
    </div>
@endsection