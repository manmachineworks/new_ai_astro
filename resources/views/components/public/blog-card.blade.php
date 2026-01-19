@props(['blog'])

<div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
    <img src="{{ $blog->cover_image ?? 'https://placehold.co/600x400?text=Blog+Image' }}" class="card-img-top"
        alt="{{ $blog->title }}" style="height: 200px; object-fit: cover;">
    <div class="card-body d-flex flex-column">
        <div class="mb-2">
            <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill table-sm">
                {{ $blog->category_name ?? 'General' }}
            </span>
            <small
                class="text-muted ms-2">{{ \Carbon\Carbon::parse($blog->published_at ?? now())->format('M d, Y') }}</small>
        </div>
        <h5 class="card-title fw-bold mb-2">
            <a href="{{ route('blogs.show', $blog->slug ?? '#') }}"
                class="text-decoration-none text-dark stretched-link">
                {{ Str::limit($blog->title, 50) }}
            </a>
        </h5>
        <p class="card-text text-secondary small flex-grow-1">
            {{ Str::limit($blog->excerpt ?? '', 100) }}
        </p>
        <div class="d-flex align-items-center mt-3 pt-3 border-top">
            <div class="flex-shrink-0">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                    style="width: 32px; height: 32px; font-size: 0.8rem;">
                    {{ substr($blog->author->name ?? 'Admin', 0, 1) }}
                </div>
            </div>
            <div class="ml-3 ms-2">
                <p class="text-xs font-medium text-dark mb-0">{{ $blog->author->name ?? 'Admin' }}</p>
                <small class="text-muted" style="font-size: 0.7rem;">Author</small>
            </div>
        </div>
    </div>
</div>