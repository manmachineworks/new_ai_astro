@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row">
            <div class="col-md-3">
                @include('astrologer.dashboard.nav')
            </div>
            <div class="col-md-9">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Appointments</h5>
                        <span class="text-muted small">Timezone: {{ $tz }}</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Date / Time</th>
                                    <th>Client</th>
                                    <th>Status</th>
                                    <th>Amount</th>
                                    <th>Notes</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($appointments as $appointment)
                                    <tr>
                                        <td>
                                            <div>{{ $appointment->start_at_utc->copy()->tz($tz)->format('M d, Y') }}</div>
                                            <small class="text-muted">{{ $appointment->start_at_utc->copy()->tz($tz)->format('h:i A') }}</small>
                                        </td>
                                        <td>
                                            <span class="text-primary fw-medium">
                                                {{ $appointment->user?->name ?: 'User #' . substr((string) $appointment->user_id, -4) }}
                                            </span>
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
                                        <td>
                                            ƒ,1{{ number_format($appointment->price_total, 2) }}
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $appointment->notes_user ? \Illuminate\Support\Str::limit($appointment->notes_user, 40) : '-' }}</span>
                                        </td>
                                        <td class="d-flex gap-2">
                                            @if($appointment->status === 'requested')
                                                <form method="POST" action="{{ route('astrologer.appointments.confirm', $appointment->id) }}">
                                                    @csrf
                                                    <button class="btn btn-sm btn-success">Confirm</button>
                                                </form>
                                                <form method="POST" action="{{ route('astrologer.appointments.decline', $appointment->id) }}">
                                                    @csrf
                                                    <button class="btn btn-sm btn-outline-danger">Decline</button>
                                                </form>
                                            @elseif($appointment->status === 'confirmed')
                                                <form method="POST" action="{{ route('astrologer.appointments.cancel', $appointment->id) }}">
                                                    @csrf
                                                    <button class="btn btn-sm btn-outline-danger">Cancel</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            No appointments scheduled yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer bg-white">
                        {{ $appointments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
