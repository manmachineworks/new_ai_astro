@props(['title', 'description' => null, 'breadcrumbs' => []])

<div class="mb-4">
    @if(count($breadcrumbs) > 0)
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}"
                        class="text-decoration-none small text-muted">Dashboard</a></li>
                @foreach($breadcrumbs as $breadcrumb)
                    @php
                        $label = is_array($breadcrumb) ? ($breadcrumb['label'] ?? '') : $breadcrumb;
                        $url = is_array($breadcrumb) ? ($breadcrumb['url'] ?? null) : null;
                    @endphp

                    @if(!$loop->last)
                        <li class="breadcrumb-item small">
                            @if($url)
                                <a href="{{ $url }}" class="text-decoration-none text-muted">{{ $label }}</a>
                            @else
                                <span class="text-muted">{{ $label }}</span>
                            @endif
                        </li>
                    @else
                        <li class="breadcrumb-item active small text-primary fw-medium" aria-current="page">{{ $label }}</li>
                    @endif
                @endforeach
            </ol>
        </nav>
    @endif

    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">{{ $title }}</h1>
            @if($description)
                <p class="text-muted small mb-0">{{ $description }}</p>
            @endif
        </div>
        @if(isset($actions))
            <div class="d-flex align-items-center gap-2">
                {{ $actions }}
            </div>
        @endif
    </div>
</div>