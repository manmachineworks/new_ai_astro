@props(['active', 'href' => '#', 'icon' => null])

@php
    $baseClasses = 'list-group-item list-group-item-action border-0 d-flex align-items-center gap-3 py-3';
    $activeClasses = 'active bg-primary text-white';
    $inactiveClasses = 'text-secondary hover-bg-light';

    $classes = $active ? "$baseClasses $activeClasses" : "$baseClasses $inactiveClasses";

    // Map icons to Bootstrap Icons (bi-*)
    $iconMap = [
        'home' => 'bi-grid-fill',
        'users' => 'bi-people-fill',
        'phone' => 'bi-telephone-fill',
        'chat' => 'bi-chat-dots-fill',
        'calendar' => 'bi-calendar-event-fill',
        'sparkles' => 'bi-stars',
        'document-text' => 'bi-file-text-fill',
        'credit-card' => 'bi-credit-card-fill',
        'user' => 'bi-person-fill',
        'support' => 'bi-life-preserver',
        'cog' => 'bi-gear-fill',
    ];

    $biIcon = $iconMap[$icon] ?? 'bi-circle';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    <i class="{{ $biIcon }} fs-5"></i>
    <span class="fw-medium">{{ $slot }}</span>
</a>