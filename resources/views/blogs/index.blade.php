@extends('layouts.public')

@section('content')
    <div class="bg-light py-5">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <h1 class="fw-bold mb-3">Insights & Articles</h1>
                    <p class="lead text-secondary">Explore the latest news, astrological tips, and cosmic guidance.</p>

                    {{-- Search --}}
                    <div class="mt-4 mx-auto" style="max-width: 500px;">
                        <form action="{{ route('blogs.index') }}" method="GET" class="position-relative">
                            <input type="text" name="search" class="form-control form-control-lg ps-5 rounded-pill"
                                placeholder="Search topics..." value="{{ request('search') }}">
                            <i
                                class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
                        </form>
                    </div>

                    {{-- Categories --}}
                    <div class="mt-4 d-flex flex-wrap justify-content-center gap-2">
                        <a href="{{ route('blogs.index') }}"
                            class="btn btn-sm rounded-pill {{ !request('category') ? 'btn-primary' : 'btn-outline-secondary' }}">All</a>
                        @foreach(['Horoscope', 'Vastu', 'Numerology', 'Tarot'] as $cat)
                            <a href="{{ route('blogs.index', ['category' => $cat]) }}"
                                class="btn btn-sm rounded-pill {{ request('category') == $cat ? 'btn-primary' : 'btn-outline-secondary' }}">{{ $cat }}</a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-5">
        <div class="row g-4">
            @forelse($blogs ?? [] as $blog)
                <div class="col-md-6 col-lg-4">
                    <x-public.blog-card :blog="$blog" />
                </div>
            @empty
                <div class="col-12 py-5 text-center">
                    <div class="mb-3"><i class="bi bi-journal-x fs-1 text-muted"></i></div>
                    <h4>No articles found</h4>
                    <p class="text-secondary">Try adjusting your search or category.</p>
                    <a href="{{ route('blogs.index') }}" class="btn btn-outline-primary">Clear Filters</a>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-5 d-flex justify-content-center">
            @if(method_exists($blogs, 'links'))
                {{ $blogs->links('pagination::bootstrap-5') }}
            @endif
        </div>
    </div>
@endsection