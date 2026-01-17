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
                        <h5 class="mb-0">Appointment Slots</h5>
                        <span class="text-muted small">Timezone: {{ $tz }}</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Start</th>
                                    <th>End</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($slots as $slot)
                                    <tr>
                                        <td>{{ $slot->start_at_utc->copy()->tz($tz)->format('M d, h:i A') }}</td>
                                        <td>{{ $slot->end_at_utc->copy()->tz($tz)->format('h:i A') }}</td>
                                        <td>
                                            <span class="badge rounded-pill
                                                @if($slot->status === 'available') bg-success
                                                @elseif($slot->status === 'blocked') bg-danger
                                                @elseif($slot->status === 'held') bg-warning text-dark
                                                @else bg-secondary @endif">
                                                {{ ucfirst($slot->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($slot->status === 'available')
                                                <form method="POST" action="{{ route('astrologer.slots.block', $slot->id) }}">
                                                    @csrf
                                                    <button class="btn btn-sm btn-outline-danger">Block</button>
                                                </form>
                                            @elseif($slot->status === 'blocked')
                                                <form method="POST" action="{{ route('astrologer.slots.unblock', $slot->id) }}">
                                                    @csrf
                                                    <button class="btn btn-sm btn-outline-success">Unblock</button>
                                                </form>
                                            @else
                                                <span class="text-muted small">Locked</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">No slots found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer bg-white">
                        {{ $slots->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
