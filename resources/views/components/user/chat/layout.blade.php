<div class="d-flex h-100 bg-white overflow-hidden">
    <div class="border-end flex-shrink-0 d-flex flex-column" style="width: 320px;">
        {{ $sidebar }}
    </div>
    <div class="flex-grow-1 d-none d-md-flex flex-column min-width-0 bg-light">
        {{ $slot }}
    </div>
</div>