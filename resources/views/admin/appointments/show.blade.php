@extends('admin.layouts.app')

@section('title', 'Appointment Details')
@section('page_title', 'Appointment Details')

@section('content')
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="text-muted">Appointment</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="text-muted small">ID</div>
                            <div class="fw-semibold">{{ $appointment->id }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Status</div>
                            <div class="fw-semibold">{{ ucfirst(str_replace('_', ' ', $appointment->status)) }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">User</div>
                            <div class="fw-semibold">{{ $appointment->user?->name ?? 'User' }}</div>
                            <small class="text-muted">{{ $appointment->user?->phone ?? '-' }}</small>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Astrologer</div>
                            <div class="fw-semibold">{{ $appointment->astrologerProfile?->display_name ?? 'Astrologer' }}</div>
                            <small class="text-muted">{{ $appointment->astrologerProfile?->user?->phone ?? '-' }}</small>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Start (UTC)</div>
                            <div class="fw-semibold">{{ $appointment->start_at_utc->format('M d, Y h:i A') }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">End (UTC)</div>
                            <div class="fw-semibold">{{ $appointment->end_at_utc->format('M d, Y h:i A') }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Pricing</div>
                            <div class="fw-semibold">
                                {{ ucfirst($appointment->pricing_mode) }} - ƒ,1{{ number_format($appointment->price_total, 2) }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Meeting Link</div>
                            <div class="fw-semibold">
                                @if($appointment->meetingLink)
                                    <a href="{{ $appointment->meetingLink->join_url }}" target="_blank">Join URL</a>
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="text-muted small">Notes (User)</div>
                            <div class="fw-semibold">{{ $appointment->notes_user ?? '-' }}</div>
                        </div>
                        <div class="col-12">
                            <div class="text-muted small">Notes (Astrologer)</div>
                            <div class="fw-semibold">{{ $appointment->notes_astrologer ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Event Timeline</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($events as $event)
                            <li class="list-group-item">
                                <div class="fw-semibold">{{ ucfirst(str_replace('_', ' ', $event->event_type)) }}</div>
                                <small class="text-muted">
                                    {{ $event->created_at?->format('M d, h:i A') }} · {{ ucfirst($event->actor_type) }}
                                </small>
                            </li>
                        @empty
                            <li class="list-group-item text-muted">No events logged.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="text-muted">Wallet Hold</h6>
                    @if($appointment->walletHold)
                        <div class="fw-semibold">Status: {{ $appointment->walletHold->status }}</div>
                        <div class="text-muted small">Amount: ƒ,1{{ number_format($appointment->walletHold->amount, 2) }}</div>
                    @else
                        <div class="text-muted">No hold</div>
                    @endif
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Admin Actions</h6>
                    <form method="POST" action="{{ route('admin.appointments.cancel', $appointment->id) }}">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label small">Reason</label>
                            <input type="text" name="reason" class="form-control" placeholder="Optional">
                        </div>
                        <button class="btn btn-danger w-100">Force Cancel & Refund</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
