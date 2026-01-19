@props(['appointment'])

<div class="card border-0 shadow-sm p-3 mb-3 hover-shadow transition">
    <div class="d-flex align-items-center">
        <img class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;"
            src="{{ $appointment['astrologer_image'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($appointment['astrologer_name'] ?? 'A') . '&color=7F9CF5&background=EBF4FF' }}"
            alt="">
        <div class="ms-3 flex-grow-1 min-width-0">
            <h6 class="mb-1 fw-bold text-dark truncate">{{ $appointment['astrologer_name'] ?? 'Astrologer' }}</h6>
            <div class="d-flex align-items-center text-muted small">
                <i class="bi bi-calendar3 me-2 text-primary"></i>
                {{ $appointment['date'] ?? 'Date TBD' }} at {{ $appointment['time'] ?? 'Time TBD' }}
            </div>
        </div>
        <div class="ms-2">
            <x-ui.badge :color="($appointment['status'] ?? '') === 'upcoming' ? 'primary' : (($appointment['status'] ?? '') === 'completed' ? 'success' : 'secondary')" :label="ucfirst($appointment['status'] ?? 'Scheduled')" />
        </div>
    </div>

    @if(($appointment['status'] ?? '') === 'upcoming' || ($appointment['status'] ?? '') === 'scheduled')
        <div class="mt-3 d-flex gap-2">
            <a href="#" class="btn btn-sm btn-primary flex-grow-1 fw-bold py-2">
                <i class="bi bi-camera-video-fill me-2"></i>Join Call
            </a>
            <button class="btn btn-sm btn-outline-secondary flex-grow-1 fw-bold py-2">
                Reschedule
            </button>
        </div>
    @endif
</div>