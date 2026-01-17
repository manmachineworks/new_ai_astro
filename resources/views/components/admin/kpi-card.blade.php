@props([
    'title',
    'value',
    'icon' => null,
    'variant' => 'primary',
    'subtitle' => null,
    'href' => null,
])

@php
    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }} @if($href) href="{{ $href }}" @endif class="text-decoration-none">
    <div class="card border-0 shadow-sm rounded-4 h-100">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between mb-3">
                <div class="bg-{{ $variant }}-subtle p-3 rounded-4">
                    <i class="{{ $icon ?? 'fas fa-chart-line' }} text-{{ $variant }}"></i>
                </div>
                @if($subtitle)
                    <span class="small fw-bold text-muted">{{ $subtitle }}</span>
                @endif
            </div>
            <h3 class="fw-bold mb-1 text-dark">{{ $value }}</h3>
            <p class="text-muted small mb-0">{{ $title }}</p>
        </div>
    </div>
</{{ $tag }}>
