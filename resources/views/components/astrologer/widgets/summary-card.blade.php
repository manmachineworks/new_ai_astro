@props(['title', 'value', 'icon', 'color' => 'primary', 'trend' => null, 'subtext' => null])

<div class="card card-premium h-100 border-0 shadow-sm transition-hover">
    <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <div>
                <span class="text-muted small text-uppercase fw-bold">{{ $title }}</span>
                <h3 class="mb-0 fw-bold mt-1 text-dark">{{ $value }}</h3>
            </div>
            <div class="rounded-circle bg-{{ $color }}-subtle p-2 d-flex align-items-center justify-content-center text-{{ $color }}"
                style="width: 40px; height: 40px;">
                <i class="{{ $icon }}"></i>
            </div>
        </div>

        @if($trend || $subtext)
            <div class="d-flex align-items-center mt-2 small">
                @if($trend)
                    <span
                        class="badge bg-{{ str_contains($trend, '-') ? 'danger' : 'success' }}-subtle text-{{ str_contains($trend, '-') ? 'danger' : 'success' }} me-2 rounded-pill">
                        {{ $trend }}
                    </span>
                @endif
                @if($subtext)
                    <span class="text-muted">{{ $subtext }}</span>
                @endif
            </div>
        @endif
    </div>
</div>