@extends('layouts.user')

@section('header')
    <x-ui.page-header title="Appointment Details" :breadcrumbs="[['label' => 'Appointments', 'url' => route('user.appointments.index')], ['label' => '#' . $appointment->id]]" />
@endsection

@section('content')
    <div class="card border-0 shadow-sm mx-auto" style="max-width: 800px;">
        <div class="card-header bg-white border-bottom py-3 px-4">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">Session Information</h6>
                <x-ui.badge :color="$appointment->status === 'scheduled' ? 'primary' : ($appointment->status === 'completed' ? 'success' : 'secondary')" :label="ucfirst(str_replace('_', ' ', $appointment->status))" />
            </div>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="text-muted small fw-medium d-block mb-1">Astrologer</label>
                    <div class="d-flex align-items-center">
                        <img src="{{ $appointment->astrologerProfile->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($appointment->astrologerProfile?->display_name ?? 'A') }}" 
                             class="rounded-circle me-2" style="width: 30px; height: 30px; object-fit: cover;">
                        <span class="fw-bold text-dark">{{ $appointment->astrologerProfile?->display_name ?? 'Astrologer' }}</span>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <label class="text-muted small fw-medium d-block mb-1">Total Amount</label>
                    <div class="fw-bold text-dark fs-5">₹{{ number_format($appointment->price_total, 2) }}</div>
                </div>

                @php($tz = config('appointments.default_timezone', 'Asia/Kolkata'))
                <div class="col-md-6">
                    <label class="text-muted small fw-medium d-block mb-1">Start Time ({{ $tz }})</label>
                    <div class="fw-bold text-dark"><i class="bi bi-calendar-check me-2 text-primary"></i>{{ $appointment->start_at_utc->copy()->tz($tz)->format('M d, Y h:i A') }}</div>
                </div>
                <div class="col-md-6">
                    <label class="text-muted small fw-medium d-block mb-1">End Time ({{ $tz }})</label>
                    <div class="fw-bold text-dark"><i class="bi bi-calendar-event me-2 text-primary"></i>{{ $appointment->end_at_utc->copy()->tz($tz)->format('M d, Y h:i A') }}</div>
                </div>

                <div class="col-md-6">
                    <label class="text-muted small fw-medium d-block mb-1">Meeting Link</label>
                    <div>
                        @if($appointment->meetingLink && $appointment->isMeetingLinkVisible())
                            <a href="{{ $appointment->meetingLink->join_url }}" target="_blank" class="btn btn-sm btn-primary">
                                <i class="bi bi-camera-video-fill me-2"></i>Join Meeting
                            </a>
                        @else
                            <span class="text-muted small italic">Link will be available 5 mins before start</span>
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="text-muted small fw-medium d-block mb-1">Booking Date</label>
                    <div class="text-dark small">{{ $appointment->created_at->format('M d, Y h:i A') }}</div>
                </div>

                <div class="col-12">
                    <label class="text-muted small fw-medium d-block mb-1">Notes from You</label>
                    <div class="p-3 bg-light rounded small text-dark border-start border-primary border-4">
                        {{ $appointment->notes_user ?? 'No notes provided' }}
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white border-top p-4 d-flex justify-content-between">
            <a href="{{ route('user.appointments.index') }}" class="btn btn-light px-4">
                <i class="bi bi-arrow-left me-2"></i>Back
            </a>
            @if($appointment->status === 'scheduled')
                <button class="btn btn-outline-danger px-4">Cancel Appointment</button>
            @endif
        </div>
    </div>
@endsection
