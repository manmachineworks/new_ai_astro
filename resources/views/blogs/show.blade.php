@extends('layouts.public')

@section('content')
    <!-- Hero Cover -->
    <div class="position-relative bg-dark" style="height: 400px;">
        <img src="{{ $blog->cover_image ?? 'https://placehold.co/1200x600' }}" alt="{{ $blog->title }}"
            class="w-100 h-100 object-fit-cover opacity-50">
        <div class="position-absolute bottom-0 start-0 w-100 p-4 p-lg-5 bg-gradient-to-t">
            <div class="container">
                <span class="badge bg-primary mb-2">{{ $blog->category_name ?? 'General' }}</span>
                <h1 class="text-white fw-bold display-5 mb-2">{{ $blog->title }}</h1>
                <div class="text-white-50 d-flex align-items-center gap-3">
                    <span><i class="bi bi-person-fill me-1"></i> {{ $blog->author->name ?? 'Admin' }}</span>
                    <span><i class="bi bi-calendar3 me-1"></i>
                        {{ \Carbon\Carbon::parse($blog->published_at ?? now())->format('F d, Y') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-5">
        <div class="row g-5">
            <div class="col-lg-8">
                <article class="blog-content lead text-secondary">
                    {!! $blog->content_html ?? $blog->content ?? 'Content coming soon...' !!}
                </article>

                <div class="mt-5 py-4 border-top border-bottom d-flex align-items-center justify-content-between">
                    <span class="fw-bold">Share this article:</span>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-facebook"></i></button>
                        <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-twitter-x"></i></button>
                        <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-whatsapp"></i></button>
                        <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-linkedin"></i></button>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="sticky-top" style="top: 100px;">
                    @if(count($relatedBlogs ?? []) > 0)
                        <h5 class="fw-bold mb-3">Related Articles</h5>
                        <div class="d-flex flex-column gap-3">
                            @foreach($relatedBlogs as $related)
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body p-3">
                                        <h6 class="card-title mb-1">
                                            <a href="{{ route('blogs.show', $related->slug) }}"
                                                class="text-decoration-none text-dark stretched-link">
                                                {{ Str::limit($related->title, 40) }}
                                            </a>
                                        </h6>
                                        <small
                                            class="text-muted">{{ $related->published_at ? \Carbon\Carbon::parse($related->published_at)->format('M d') : '' }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="mt-5 p-4 bg-light rounded shadow-sm text-center">
                        <h5 class="fw-bold">Need Personal Guidance?</h5>
                        <p class="text-secondary small">Connect with our expert astrologers today.</p>
                        <a href="{{ route('user.astrologers.index') }}" class="btn btn-primary w-100">Talk to Astrologer</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection