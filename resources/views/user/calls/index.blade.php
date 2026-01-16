@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4 mb-0">My Call History</h2>
        </div>

        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Date</th>
                            <th>Astrologer</th>
                            <th>Duration</th>
                            <th>Charges</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($calls as $call)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $call->created_at->format('M d, Y') }}</div>
                                    <small class="text-muted">{{ $call->created_at->format('h:i A') }}</small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                            style="width: 32px; height: 32px;">
                                            {{ substr($call->astrologerProfile->display_name, 0, 1) }}
                                        </div>
                                        <span>{{ $call->astrologerProfile->display_name }}</span>
                                    </div>
                                </td>
                                <td>
                                    @if($call->status === 'completed')
                                        {{ floor($call->duration_seconds / 60) }}m {{ $call->duration_seconds % 60 }}s
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($call->gross_amount > 0)
                                        <span class="fw-bold">₹{{ number_format($call->gross_amount, 2) }}</span>
                                    @else
                                        <span class="text-muted">₹0.00</span>
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
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    No calls found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-0">
                {{ $calls->links() }}
            </div>
        </div>
    </div>
@endsection