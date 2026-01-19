@props(['report'])

<div class="card border-0 shadow-sm h-100 transition hover-shadow">
    <div class="card-body p-4 d-flex flex-column">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <h5 class="fw-bold text-dark mb-0 pe-2">{{ $report['title'] ?? 'Astrology Report' }}</h5>
            <x-ui.badge :color="($report['purchased'] ?? false) ? 'success' : 'primary'" :label="($report['purchased'] ?? false) ? 'Purchased' : 'Available'" />
        </div>

        <p class="text-muted small mb-0 flex-grow-1">
            {{ $report['description'] ?? 'Detailed insights and predictions based on your birth chart.' }}
        </p>

        <div class="mt-4 pt-4 border-top">
            @if($report['purchased'] ?? false)
                <button
                    class="btn btn-outline-primary w-100 fw-bold d-flex align-items-center justify-content-center gap-2 py-2">
                    <i class="bi bi-file-earmark-pdf"></i>
                    Download PDF
                </button>
            @else
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-primary fw-bold fs-4">₹{{ $report['price'] ?? '0' }}</span>
                    </div>
                    <button class="btn btn-primary fw-bold px-4 py-2 shadow-sm">
                        Buy Now
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .transition {
        transition: all 0.2s ease;
    }

    .hover-shadow:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
    }
</style>