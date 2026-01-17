@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">Edit Post: {{ $post->title }}</h1>
            <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="btn btn-outline-primary btn-sm">View</a>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card shadow mb-4">
            <div class="card-body">
                <form action="{{ route('admin.blog.posts.update', $post->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" name="title" class="form-control"
                                    value="{{ old('title', $post->title) }}" required>
                            </div>
                            <div class="form-group">
                                <label>Content (HTML)</label>
                                <textarea name="content_html" class="form-control" rows="15"
                                    required>{{ old('content_html', $post->content_html) }}</textarea>
                            </div>
                            <div class="form-group">
                                <label>Excerpt</label>
                                <textarea name="excerpt" class="form-control"
                                    rows="3">{{ old('excerpt', $post->excerpt) }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light mb-3">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Slug</label>
                                        <input type="text" name="slug" class="form-control"
                                            value="{{ old('slug', $post->slug) }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Category</label>
                                        <select name="blog_category_id" class="form-control" required>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('blog_category_id', $post->blog_category_id) == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }} ({{ $category->locale }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Locale</label>
                                        <select name="locale" class="form-control">
                                            <option value="en" {{ old('locale', $post->locale) == 'en' ? 'selected' : '' }}>
                                                English</option>
                                            <option value="hi" {{ old('locale', $post->locale) == 'hi' ? 'selected' : '' }}>
                                                Hindi</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Featured Image</label>
                                        @if($post->featured_image_path)
                                            <img src="{{ Storage::url($post->featured_image_path) }}" class="img-fluid mb-2">
                                        @endif
                                        <input type="file" name="featured_image" class="form-control-file">
                                    </div>
                                    <div class="form-group form-check">
                                        <input type="checkbox" name="publish" class="form-check-input" id="publishCheck"
                                            value="1" {{ old('publish', $post->status === 'published') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="publishCheck">Published</label>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block mt-3">Update Post</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection