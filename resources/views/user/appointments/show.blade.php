@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Appointment Details</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-muted small">Astrologer</div>
                        <div class="fw-semibold">{{ $appointment->astrologerProfile?->display_name ?? 'Astrologer' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Status</div>
                        <div class="fw-semibold">{{ ucfirst(str_replace('_', ' ', $appointment->status)) }}</div>
                    </div>
                    @php($tz = config('appointments.default_timezone', 'Asia/Kolkata'))
                    <div class="col-md-6">
                        <div class="text-muted small">Start ({{ $tz }})</div>
                        <div class="fw-semibold">{{ $appointment->start_at_utc->copy()->tz($tz)->format('M d, Y h:i A') }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">End ({{ $tz }})</div>
                        <div class="fw-semibold">{{ $appointment->end_at_utc->copy()->tz($tz)->format('M d, Y h:i A') }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Amount</div>
                        <div class="fw-semibold">ƒ,1{{ number_format($appointment->price_total, 2) }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Meeting Link</div>
                        <div class="fw-semibold">
                            @if($appointment->meetingLink && $appointment->isMeetingLinkVisible())
                                <a href="{{ $appointment->meetingLink->join_url }}" target="_blank">Join</a>
                            @else
                                -
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Notes</div>
                        <div class="fw-semibold">{{ $appointment->notes_user ?? '-' }}</div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white">
                <a href="{{ route('appointments.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
            </div>
        </div>
    </div>
@endsection
