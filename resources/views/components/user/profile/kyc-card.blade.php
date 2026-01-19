@props(['status', 'title', 'document' => null])

<div class="card border border-light shadow-none rounded-3 mb-2">
    <div class="card-body p-3 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <div class="rounded-3 bg-light d-flex align-items-center justify-content-center border"
                style="width: 45px; height: 45px;">
                <i class="bi bi-file-earmark-text fs-4 text-muted"></i>
            </div>
            <div class="ms-3">
                <h6 class="mb-0 fw-bold text-dark small">{{ $title }}</h6>
                <div class="d-flex align-items-center mt-1">
                    @if($status === 'approved')
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1"
                            style="font-size: 0.65rem;">
                            <i class="bi bi-check-circle-fill me-1"></i>Approved
                        </span>
                    @elseif($status === 'pending')
                        <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2 py-1"
                            style="font-size: 0.65rem;">
                            <i class="bi bi-clock-history me-1"></i>In Review
                        </span>
                    @else
                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2 py-1"
                            style="font-size: 0.65rem;">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>Action Required
                        </span>
                    @endif
                </div>
            </div>
        </div>
        <div>
            @if($status === 'approved')
                <i class="bi bi-check-lg text-success fs-4"></i>
            @elseif($status === 'pending')
                <i class="bi bi-hourglass-split text-warning fs-5"></i>
            @else
                <button class="btn btn-sm btn-outline-primary fw-bold">Upload</button>
            @endif
        </div>
    </div>
</div>