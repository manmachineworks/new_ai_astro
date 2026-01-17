@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">My Appointments</h5>
                <span class="text-muted small">Timezone: {{ $tz }}</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Date / Time</th>
                            <th>Astrologer</th>
                            <th>Status</th>
                            <th>Amount</th>
                            <th>Details</th>
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
                                    {{ $appointment->astrologerProfile?->display_name ?? 'Astrologer' }}
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
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('appointments.show', $appointment->id) }}">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">No appointments yet.</td>
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
@endsection
