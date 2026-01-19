@props(['title', 'subtitle' => null, 'center' => false])

<div class="mb-5 {{ $center ? 'text-center' : '' }}">
    <h2 class="fw-bold display-6 mb-2">{{ $title }}</h2>
    @if($subtitle)
        <p class="text-secondary lead fs-6">{{ $subtitle }}</p>
    @endif
    <div class="d-flex {{ $center ? 'justify-content-center' : '' }}">
        <div class="bg-primary" style="height: 4px; width: 60px; border-radius: 2px;"></div>
    </div>
</div>