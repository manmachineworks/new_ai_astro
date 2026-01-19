@props(['astrologer'])

<div class="card border-0 shadow-sm mt-4">
    <div class="card-body p-4">
        <h5 class="fw-bold text-dark mb-4">Availability</h5>
        <div class="row g-2 text-center small">
            <div class="col-4">
                <div
                    class="p-2 bg-primary bg-opacity-10 text-primary rounded border border-primary border-opacity-25 h-100 d-flex flex-column justify-content-center">
                    <div class="opacity-75">Today</div>
                    <div class="fw-bold">10 AM - 2 PM</div>
                </div>
            </div>
            <div class="col-4">
                <div class="p-2 border rounded h-100 d-flex flex-column justify-content-center">
                    <div class="text-muted">Tomorrow</div>
                    <div class="fw-bold text-dark">4 PM - 8 PM</div>
                </div>
            </div>
            <div class="col-4">
                <div class="p-2 border rounded h-100 d-flex flex-column justify-content-center opacity-50 bg-light">
                    <div class="text-muted">Fri, Oct 30</div>
                    <div class="fw-bold text-dark italic">Unavailable</div>
                </div>
            </div>
        </div>

        <div class="mt-4 text-center">
            <a href="{{ route('user.appointments.book', $astrologer['id'] ?? 1) }}"
                class="btn btn-link text-primary text-decoration-none small fw-bold p-0">
                View full schedule <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
</div>