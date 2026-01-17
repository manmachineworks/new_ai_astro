@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Blog Categories</h1>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <div class="col-md-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Create Category</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.blog.categories.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Slug</label>
                                <input type="text" name="slug" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Locale</label>
                                <select name="locale" class="form-control">
                                    <option value="en">English</option>
                                    <option value="hi">Hindi</option>
                                </select>
                            </div>
                            <div class="form-group form-check">
                                <input type="checkbox" name="is_active" class="form-check-input" id="activeCheck" checked>
                                <label class="form-check-label" for="activeCheck">Active</label>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Add Category</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Categories List</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Slug</th>
                                        <th>Locale</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($categories as $category)
                                        <tr>
                                            <td>{{ $category->name }}</td>
                                            <td>{{ $category->slug }}</td>
                                            <td><span class="badge badge-info">{{ strtoupper($category->locale) }}</span></td>
                                            <td>
                                                @if($category->is_active)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <!-- Edit Modal Trigger -->
                                                <button type="button" class="btn btn-sm btn-info" data-toggle="modal"
                                                    data-target="#editModal{{ $category->id }}">
                                                    Edit
                                                </button>
                                                <form action="{{ route('admin.blog.categories.destroy', $category->id) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Delete category?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Del</button>
                                                </form>

                                                <!-- Edit Modal -->
                                                <div class="modal fade" id="editModal{{ $category->id }}" tabindex="-1"
                                                    role="dialog" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <form
                                                                action="{{ route('admin.blog.categories.update', $category->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Edit Category</h5>
                                                                    <button type="button" class="close" data-dismiss="modal"
                                                                        aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="form-group">
                                                                        <label>Name</label>
                                                                        <input type="text" name="name" class="form-control"
                                                                            value="{{ $category->name }}" required>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Slug</label>
                                                                        <input type="text" name="slug" class="form-control"
                                                                            value="{{ $category->slug }}" required>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Locale</label>
                                                                        <select name="locale" class="form-control">
                                                                            <option value="en" {{ $category->locale == 'en' ? 'selected' : '' }}>English</option>
                                                                            <option value="hi" {{ $category->locale == 'hi' ? 'selected' : '' }}>Hindi</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="form-group form-check">
                                                                        <input type="checkbox" name="is_active"
                                                                            class="form-check-input" {{ $category->is_active ? 'checked' : '' }}>
                                                                        <label class="form-check-label">Active</label>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-dismiss="modal">Close</button>
                                                                    <button type="submit" class="btn btn-primary">Save
                                                                        Changes</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No categories found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{ $categories->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection