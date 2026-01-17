@extends('admin.layouts.app')

@section('title', $title)

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="fw-bold m-0">{{ $title }}</h2>
                <div class="text-muted small">{{ $description }}</div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body">
                <p class="mb-0 text-muted">This section is under construction.</p>
            </div>
        </div>
    </div>
@endsection
