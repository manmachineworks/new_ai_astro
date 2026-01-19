@props(['title', 'link' => '#', 'linkText' => 'View All'])

<div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="fw-bold mb-0 text-dark">{{ $title }}</h4>
    <a href="{{ $link }}" class="text-decoration-none fw-medium">
        {{ $linkText }} <i class="bi bi-arrow-right ms-1"></i>
    </a>
</div>