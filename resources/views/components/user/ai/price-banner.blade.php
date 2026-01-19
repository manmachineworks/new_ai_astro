@props(['price'])

<div class="bg-primary rounded-3 p-3 text-white shadow-sm d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center">
        <i class="bi bi-lightning-charge-fill fs-4 me-3 opacity-75"></i>
        <div>
            <h6 class="mb-0 fw-bold">AI Astrologer</h6>
            <small class="opacity-75">Instant answers 24/7</small>
        </div>
    </div>
    <div class="text-end">
        <span class="fs-5 fw-bold">₹{{ $price }}</span>
        <small class="d-block opacity-75" style="font-size: 0.7rem;">per query</small>
    </div>
</div>