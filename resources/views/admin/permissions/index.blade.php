@extends('admin.layouts.app')

@section('title', 'Permissions')
@section('page_title', 'Permission Manager')

@section('content')
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>Create Permission</h6>
                    <form method="POST" action="{{ route('admin.permissions.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Permission name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <button class="btn btn-primary">Create</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>Permissions</h6>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($permissions as $permission)
                                <tr>
                                    <td>{{ $permission->name }}</td>
                                    <td class="text-end">
                                        <form method="POST" action="{{ route('admin.permissions.destroy', $permission) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete permission?')">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
