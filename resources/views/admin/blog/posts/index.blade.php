@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">Blog Posts</h1>
            <a href="{{ route('admin.blog.posts.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> New Post
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Locale</th>
                                <th>Status</th>
                                <th>Published</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($posts as $post)
                                <tr>
                                    <td>{{ $post->title }}</td>
                                    <td>{{ $post->category->name ?? 'Uncategorized' }}</td>
                                    <td><span class="badge badge-info">{{ strtoupper($post->locale) }}</span></td>
                                    <td>
                                        @if($post->status === 'published')
                                            <span class="badge badge-success">Published</span>
                                        @else
                                            <span class="badge badge-secondary">Draft</span>
                                        @endif
                                    </td>
                                    <td>{{ $post->published_at ? $post->published_at->format('Y-m-d') : '-' }}</td>
                                    <td>
                                        <a href="{{ route('admin.blog.posts.edit', $post->id) }}"
                                            class="btn btn-sm btn-info">Edit</a>
                                        <form action="{{ route('admin.blog.posts.destroy', $post->id) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Delete post?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Del</button>
                                        </form>
                                        <a href="{{ route('blog.show', $post->slug) }}" target="_blank"
                                            class="btn btn-sm btn-light">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No posts found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $posts->links() }}
            </div>
        </div>
    </div>
@endsection