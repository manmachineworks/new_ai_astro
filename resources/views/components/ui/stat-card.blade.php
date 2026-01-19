@props(['title', 'value', 'icon' => null, 'trend' => null, 'trendType' => 'neutral'])

<div {{ $attributes->merge(['class' => 'card h-100 border-0 shadow-sm']) }}>
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h6 class="card-subtitle text-muted text-uppercase small">{{ $title }}</h6>
            @if($icon)
                <div class="text-primary bg-primary-subtle rounded p-2 d-flex align-items-center justify-content-center"
                    style="width: 36px; height: 36px;">
                    {{ $icon }}
                </div>
            @endif
        </div>
        <h3 class="card-title mb-0 fw-bold text-dark">{{ $value }}</h3>

        @if($trend)
            <div class="mt-2 small">
                @php
                    $trendColor = match ($trendType) {
                        'up', 'success' => 'text-success',
                        'down', 'danger' => 'text-danger',
                        default => 'text-muted'
                    };
                    $trendIcon = match ($trendType) {
                        'up', 'success' => 'bi-arrow-up',
                        'down', 'danger' => 'bi-arrow-down',
                        default => ''
                    };
                @endphp
                <span class="{{ $trendColor }} fw-medium">
                    <i class="bi {{ $trendIcon }}"></i> {{ $trend }}
                </span>
                <span class="text-muted ms-1">vs last period</span>
            </div>
        @endif

        {{ $slot }}
    </div>
</div>