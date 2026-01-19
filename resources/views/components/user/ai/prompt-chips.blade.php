@props(['chips' => ['What is my daily horoscope?', 'Will I get promoted?', 'Love life prediction', 'Lucky color for today']])

<div class="d-flex flex-wrap gap-2 py-2">
    @foreach($chips as $chip)
        <button type="button"
            class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 text-xs fw-medium shadow-sm transition-all border-opacity-25"
            style="font-size: 0.75rem;">
            {{ $chip }}
            <i class="bi bi-arrow-right-short ms-1"></i>
        </button>
    @endforeach
</div>