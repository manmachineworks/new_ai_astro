@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row">
            <div class="col-md-3">
                @include('astrologer.dashboard.nav')
            </div>
            <div class="col-md-9">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Call History & Earnings</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Client</th>
                                    <th>Duration</th>
                                    <th>Your Earnings</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($calls as $call)
                                    <tr>
                                        <td>
                                            <div>{{ $call->created_at->format('M d, Y') }}</div>
                                            <small class="text-muted">{{ $call->created_at->format('h:i A') }}</small>
                                        </td>
                                        <td>
                                            <span class="text-primary fw-medium">
                                                {{ $call->user_masked_identifier ?: 'User #' . substr($call->user_id, -4) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($call->status === 'completed')
                                                {{ floor($call->duration_seconds / 60) }}m {{ $call->duration_seconds % 60 }}s
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if($call->astrologer_earnings_amount > 0)
                                                <span
                                                    class="text-success fw-bold">₹{{ number_format($call->astrologer_earnings_amount, 2) }}</span>
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
                                            No calls recorded yet.
                                        </td>
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
        </div>
    </div>
@endsection