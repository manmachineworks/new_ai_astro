@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">Edit Page: {{ $page->title }}</h1>
            <a href="{{ route('cms.page', $page->slug) }}" target="_blank" class="btn btn-outline-primary btn-sm">View
                Public</a>
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
                <form action="{{ route('admin.cms.pages.update', $page->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" name="title" class="form-control"
                                    value="{{ old('title', $page->title) }}" required>
                            </div>
                            <div class="form-group">
                                <label>Content (HTML)</label>
                                <textarea name="content_html" class="form-control" rows="15"
                                    required>{{ old('content_html', $page->content_html) }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light mb-3">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Slug</label>
                                        <input type="text" name="slug" class="form-control"
                                            value="{{ old('slug', $page->slug) }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Locale</label>
                                        <select name="locale" class="form-control">
                                            <option value="en" {{ old('locale', $page->locale) == 'en' ? 'selected' : '' }}>
                                                English</option>
                                            <option value="hi" {{ old('locale', $page->locale) == 'hi' ? 'selected' : '' }}>
                                                Hindi</option>
                                        </select>
                                    </div>
                                    <div class="form-group form-check">
                                        <input type="checkbox" name="publish" class="form-check-input" id="publishCheck"
                                            value="1" {{ old('publish', $page->status === 'published') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="publishCheck">Published</label>
                                    </div>
                                </div>
                            </div>

                            <div class="card bg-light">
                                <div class="card-header">SEO Settings</div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Meta Title</label>
                                        <input type="text" name="meta_title" class="form-control"
                                            value="{{ old('meta_title', $page->meta_title) }}">
                                    </div>
                                    <div class="form-group">
                                        <label>Meta Description</label>
                                        <textarea name="meta_description" class="form-control"
                                            rows="3">{{ old('meta_description', $page->meta_description) }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block mt-3">Update Page</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection