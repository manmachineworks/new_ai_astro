@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent px-0">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('blog.index') }}">Blog</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $post->title }}</li>
                    </ol>
                </nav>

                <h1 class="display-4 font-weight-bold mb-3">{{ $post->title }}</h1>
                <p class="text-muted mb-4">
                    Published on {{ $post->published_at->format('M d, Y') }}
                    in <a href="{{ route('blog.category', $post->category->slug) }}">{{ $post->category->name }}</a>
                </p>

                @if($post->featured_image_path)
                    <img src="{{ Storage::url($post->featured_image_path) }}" class="img-fluid rounded mb-4 w-100 shadow-sm"
                        alt="{{ $post->title }}">
                @endif

                <div class="article-content" style="font-size: 1.1rem; line-height: 1.8;">
                    {!! $post->content_html !!}
                </div>

                <hr class="my-5">

                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('blog.index') }}" class="btn btn-outline-secondary">&larr; Back to Blog</a>

                    @if($post->category)
                        <a href="{{ route('blog.category', $post->category->slug) }}" class="btn btn-outline-primary">
                            More in {{ $post->category->name }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection