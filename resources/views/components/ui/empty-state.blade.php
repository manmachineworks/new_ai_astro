@props(['title', 'description', 'action' => null, 'icon' => 'bi-inbox'])

<div {{ $attributes->merge(['class' => 'text-center py-5']) }}>
    <div class="mb-3 text-muted">
        <i class="bi {{ $icon }} display-4"></i>
    </div>
    <h5 class="fw-bold text-dark">{{ $title }}</h5>
    <p class="text-muted mb-4">{{ $description }}</p>
    @if($action)
        <div>
            {{ $action }}
        </div>
    @endif
</div>