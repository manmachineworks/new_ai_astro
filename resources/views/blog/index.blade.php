@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row">
            <!-- Main Content -->
            <div class="col-md-8">
                <h1 class="mb-4">{{ __('messages.blog_title') ?? 'Blog' }}</h1>

                @forelse($posts as $post)
                    <div class="card mb-4 shadow-sm border-0">
                        @if($post->featured_image_path)
                            <img src="{{ Storage::url($post->featured_image_path) }}" class="card-img-top" alt="{{ $post->title }}">
                        @endif
                        <div class="card-body">
                            <h2 class="card-title h4">
                                <a href="{{ route('blog.show', $post->slug) }}" class="text-dark text-decoration-none">
                                    {{ $post->title }}
                                </a>
                            </h2>
                            <p class="text-muted small">
                                {{ $post->published_at ? $post->published_at->format('M d, Y') : '' }} |
                                <a href="{{ route('blog.category', $post->category->slug) }}" class="text-muted">
                                    {{ $post->category->name }}
                                </a>
                            </p>
                            <p class="card-text">{{ Str::limit($post->excerpt, 150) }}</p>
                            <a href="{{ route('blog.show', $post->slug) }}" class="btn btn-primary btn-sm rounded-pill">Read
                                More &rarr;</a>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-info">
                        No posts found in this language.
                    </div>
                @endforelse

                {{ $posts->links() }}
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Categories</h5>
                    </div>
                    <ul class="list-group list-group-flush">
                        @foreach($categories as $category)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <a href="{{ route('blog.category', $category->slug) }}" class="text-decoration-none text-dark">
                                    {{ $category->name }}
                                </a>
                                <span class="badge badge-primary badge-pill">{{ $category->posts_count }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection