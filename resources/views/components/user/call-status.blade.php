@props(['status' => 'connecting', 'astrologerName'])

<div class="card border-0 shadow-lg text-center p-5 rounded-4 overflow-hidden">
    <div class="card-body py-4">
        <div class="position-relative d-inline-block mb-4">
            <div
                class="position-absolute top-50 start-50 translate-middle w-100 h-100 bg-primary rounded-circle animate-ping opacity-25">
            </div>
            <img class="rounded-circle border border-4 border-white shadow relative z-index-1"
                style="width: 120px; height: 120px; object-fit: cover;"
                src="https://ui-avatars.com/api/?name={{ urlencode($astrologerName) }}&color=7F9CF5&background=EBF4FF"
                alt="">
        </div>

        <h3 class="fw-bold text-dark mb-2">{{ $astrologerName }}</h3>
        <p class="text-primary fw-bold text-uppercase tracking-wider animate-pulse mb-0">
            <span class="spinner-grow spinner-grow-sm me-2" role="status"></span>
            {{ $status }}...
        </p>

        <div class="mt-5">
            <button class="btn btn-danger rounded-circle p-3 shadow-lg hover-scale transition">
                <i class="bi bi-telephone-x-fill fs-3"></i>
            </button>
            <p class="text-muted small mt-3">End Call</p>
        </div>
    </div>
</div>

<style>
    @keyframes ping {

        75%,
        100% {
            transform: translate(-50%, -50%) scale(2);
            opacity: 0;
        }
    }

    .animate-ping {
        animation: ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;
    }

    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: .5;
        }
    }

    .hover-scale:hover {
        transform: scale(1.1);
    }

    .transition {
        transition: all 0.2s ease;
    }

    .z-index-1 {
        z-index: 1;
    }
</style>