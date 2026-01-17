@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Manage Banners</h1>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row">
            <!-- Create Form -->
            <div class="col-md-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Add New Banner</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.cms.banners.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label>Title (Optional)</label>
                                <input type="text" name="title" class="form-control" value="{{ old('title') }}">
                            </div>
                            <div class="form-group">
                                <label>Image</label>
                                <input type="file" name="image" class="form-control-file" required>
                                <small class="text-muted">Max 2MB</small>
                            </div>
                            <div class="form-group">
                                <label>Link URL</label>
                                <input type="url" name="link_url" class="form-control" value="{{ old('link_url') }}">
                            </div>
                            <div class="form-group">
                                <label>Position</label>
                                <select name="position" class="form-control">
                                    <option value="home_top">Home Top Slider</option>
                                    <option value="home_mid">Home Middle</option>
                                    <option value="home_bottom">Home Bottom</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Locale</label>
                                <select name="locale" class="form-control">
                                    <option value="en">English</option>
                                    <option value="hi">Hindi</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Add Banner</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- List -->
            <div class="col-md-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Existing Banners</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Info</th>
                                        <th>Locale</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($banners as $banner)
                                        <tr>
                                            <td>
                                                <img src="{{ Storage::url($banner->image_path) }}" width="100"
                                                    class="img-thumbnail">
                                            </td>
                                            <td>
                                                <strong>{{ $banner->title ?? 'No Title' }}</strong><br>
                                                <small>{{ $banner->position }}</small><br>
                                                <small class="text-muted"><a href="{{ $banner->link_url }}"
                                                        target="_blank">{{ Str::limit($banner->link_url, 30) }}</a></small>
                                            </td>
                                            <td><span class="badge badge-info">{{ strtoupper($banner->locale) }}</span></td>
                                            <td>
                                                <form action="{{ route('admin.cms.banners.destroy', $banner->id) }}"
                                                    method="POST" onsubmit="return confirm('Delete banner?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"><i
                                                            class="fas fa-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No banners found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{ $banners->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection