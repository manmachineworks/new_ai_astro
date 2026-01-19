@extends('layouts.user')

@section('content')
    <div class="mx-auto py-5" style="max-width: 550px;">
        <div class="card border-0 shadow-lg overflow-hidden rounded-4">
            <div class="bg-primary px-4 py-5 text-center text-white">
                <div class="bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                    style="width: 70px; height: 70px;">
                    <i class="bi bi-telephone-x-fill fs-2"></i>
                </div>
                <h2 class="fw-bold mb-1">Call Summary</h2>
                <p class="text-white-50 mb-0">Consultation with Astro Priya completed</p>
            </div>

            <div class="card-body p-4 p-md-5">
                <div class="row g-3 mb-5">
                    <div class="col-6">
                        <div class="p-3 bg-light rounded-4 text-center">
                            <div class="text-muted small text-uppercase tracking-wider fw-bold mb-1">Duration</div>
                            <div class="h4 fw-bold text-dark mb-0">12m 30s</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-light rounded-4 text-center">
                            <div class="text-muted small text-uppercase tracking-wider fw-bold mb-1">Total Cost</div>
                            <div class="h4 fw-bold text-primary mb-0">₹350.00</div>
                        </div>
                    </div>
                </div>

                <div class="text-center mb-5">
                    <h5 class="fw-bold text-dark mb-4">Rate your experience</h5>
                    <div class="d-flex justify-content-center gap-2 mb-2">
                        @foreach(range(1, 5) as $star)
                            <button class="btn p-0 border-0 text-warning hover-scale transition">
                                <i class="bi bi-star-fill" style="font-size: 2.2rem;"></i>
                            </button>
                        @endforeach
                    </div>
                    <p class="text-muted small">Your feedback helps us improve</p>
                </div>

                <div class="row g-2">
                    <div class="col-6">
                        <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary w-100 py-2 fw-bold">
                            Go Home
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('user.astrologers.index') }}"
                            class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
                            Consult Again
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<style>
    .hover-scale:hover {
        transform: scale(1.15);
    }

    .transition {
        transition: all 0.2s ease;
    }
</style>