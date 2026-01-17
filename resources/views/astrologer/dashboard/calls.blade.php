@extends('layouts.astrologer')

@section('title', 'Call History')
@section('page-title', 'Call History')

@section('content')
    <div class="card card-premium mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 rounded-start">Date & Time</th>
                            <th class="border-0">User</th>
                            <th class="border-0">Duration</th>
                            <th class="border-0">Status</th>
                            <th class="border-0">Earnings</th>
                            <th class="border-0 rounded-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($calls as $call)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $call->created_at->format('M d, Y') }}</div>
                                    <div class="small text-muted">{{ $call->created_at->format('h:i A') }}</div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-circle-sm bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center"
                                            style="width:32px;height:32px;">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div>
                                            <!-- PII MASKING: Use masked identifier or fallback -->
                                            @php
                                                $displayName = $call->user_masked_identifier ?? ('User #' . $call->user_id);
                                                if (is_string($displayName) && str_contains($displayName, '@')) {
                                                    $displayName = 'User #' . $call->user_id;
                                                }
                                            @endphp
                                            <div class="fw-bold text-dark">{{ $displayName }}</div>
                                            <div class="small text-muted">ID: {{ substr($call->user_id, 0, 8) }}...</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($call->duration_seconds > 0)
                                        {{ gmdate('H:i:s', $call->duration_seconds) }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($call->status == 'completed')
                                        <span class="badge bg-success-subtle text-success rounded-pill px-3">Completed</span>
                                    @elseif($call->status == 'missed')
                                        <span class="badge bg-danger-subtle text-danger rounded-pill px-3">Missed</span>
                                    @elseif($call->status == 'failed')
                                        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3">Failed</span>
                                    @else
                                        <span
                                            class="badge bg-warning-subtle text-warning rounded-pill px-3">{{ ucfirst($call->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold text-success">
                                        ₹ {{ number_format($call->astrologer_earnings_amount ?? 0, 2) }}
                                    </div>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-light border" title="View Details">
                                        <i class="fas fa-eye text-muted"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted mb-2"><i class="fas fa-phone-slash fa-2x"></i></div>
                                    <p>No call history found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $calls->links() }}
            </div>
        </div>
    </div>
@endsection
