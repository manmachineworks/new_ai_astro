@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">CMS Pages</h1>
            <a href="{{ route('admin.cms.pages.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create New Page
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Slug</th>
                                <th>Locale</th>
                                <th>Status</th>
                                <th>Last Updated</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pages as $page)
                                <tr>
                                    <td>{{ $page->title }}</td>
                                    <td>{{ $page->slug }}</td>
                                    <td><span class="badge badge-info">{{ strtoupper($page->locale) }}</span></td>
                                    <td>
                                        @if($page->status === 'published')
                                            <span class="badge badge-success">Published</span>
                                        @else
                                            <span class="badge badge-secondary">Draft</span>
                                        @endif
                                    </td>
                                    <td>{{ $page->updated_at->format('Y-m-d') }}</td>
                                    <td>
                                        <a href="{{ route('admin.cms.pages.edit', $page->id) }}"
                                            class="btn btn-sm btn-info">Edit</a>
                                        <form action="{{ route('admin.cms.pages.destroy', $page->id) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                        <a href="{{ route('cms.page', ['slug' => $page->slug]) }}?lang={{ $page->locale }}"
                                            target="_blank" class="btn btn-sm btn-light">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No pages found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $pages->links() }}
            </div>
        </div>
    </div>
@endsection