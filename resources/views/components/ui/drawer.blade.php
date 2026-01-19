@props(['id', 'maxWidth' => 'md', 'title' => ''])

@php
    $maxWidthClass = match ($maxWidth) {
        'sm', 'md' => 'offcanvas-end', // Default width
        'lg', 'xl', '2xl', 'full' => 'offcanvas-end w-100', // Full width for larger
        default => 'offcanvas-end'
    };
    // Bootstrap offcanvas doesn't support fine-grained widths like 'md' natively without custom CSS, 
    // so we'll stick to standard offcanvas standard width (400px) or w-100.
@endphp

<div x-data="{ open: false }" x-on:open-drawer.window="$event.detail == '{{ $id }}' ? open = true : null"
    x-on:close-drawer.window="$event.detail == '{{ $id }}' ? open = false : null" class="offcanvas {{ $maxWidthClass }}"
    :class="{ 'show': open }" style="visibility: visible;" {{-- Alpine controls logic, but we need it visible if true
    --}} x-show="open" tabindex="-1" aria-labelledby="offcanvasRightLabel">

    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasRightLabel">{{ $title }}</h5>
        <button type="button" class="btn-close" @click="open = false" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body">
        {{ $slot }}
    </div>
</div>
<!-- Backdrop handled by Bootstrap usually, but with manual control we might need a custom backdrop or just let Bootstrap JS handle it. 
     Since we are using Alpine to toggle 'show', we might miss the backdrop. 
     Ideally for offcanvas, we should use data-bs-toggle. 
     If we want to keep `open-drawer` event logic, we might need a backdrop div here manually.
-->
<div x-show="open" class="offcanvas-backdrop fade show" @click="open = false"></div>