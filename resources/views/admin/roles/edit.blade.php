@extends('admin.layouts.app')

@section('title', 'Edit Role')
@section('page_title', 'Edit Role')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.roles.update', $role) }}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Role name</label>
                    <input type="text" name="name" class="form-control" value="{{ $role->name }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Permissions</label>
                    <div class="row">
                        @foreach($permissions as $permission)
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]"
                                        value="{{ $permission->name }}" id="perm-{{ $permission->id }}"
                                        @checked($role->hasPermissionTo($permission->name))>
                                    <label class="form-check-label" for="perm-{{ $permission->id }}">
                                        {{ $permission->name }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <button class="btn btn-primary">Update</button>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">Back</a>
            </form>
        </div>
    </div>
@endsection
