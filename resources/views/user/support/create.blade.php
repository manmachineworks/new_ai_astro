@extends('layouts.user')

@section('header')
    <x-ui.page-header title="Create Ticket" :breadcrumbs="[['label' => 'Support', 'url' => route('user.support.index')], ['label' => 'Create']]" />
@endsection

@section('content')
    <div class="card border-0 shadow-sm mx-auto" style="max-width: 800px;">
        <div class="card-body p-4">
            <form action="{{ route('user.support.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="form-label small fw-medium text-muted">Category</label>
                    <select name="category" class="form-select">
                        <option>Payment Issue</option>
                        <option>Call Connectivity</option>
                        <option>App Bug</option>
                        <option>Other</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-medium text-muted">Subject</label>
                    <input type="text" name="subject" class="form-control" placeholder="Brief summary of the issue">
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-medium text-muted">Description</label>
                    <textarea name="description" rows="4" class="form-control"
                        placeholder="Please describe your issue in detail..."></textarea>
                </div>

                {{-- Attachments placeholder --}}
                <div class="mb-4">
                    <label class="form-label small fw-medium text-muted">Attachments (Optional)</label>
                    <div class="border-2 border-dashed border-light rounded-3 p-5 text-center bg-light bg-opacity-50">
                        <i class="bi bi-cloud-arrow-up fs-1 text-muted mb-3 d-block"></i>
                        <div class="d-flex justify-content-center align-items-center mb-2">
                            <label for="file-upload" class="btn btn-sm btn-outline-primary position-relative">
                                <span>Upload a file</span>
                                <input id="file-upload" name="file-upload" type="file"
                                    class="position-absolute top-0 start-0 opacity-0 w-100 h-100" style="cursor: pointer;">
                            </label>
                            <span class="ms-2 text-muted small">or drag and drop</span>
                        </div>
                        <p class="text-muted small mb-0">PNG, JPG, PDF up to 5MB</p>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 pt-2">
                    <a href="{{ route('user.support.index') }}" class="btn btn-light px-4">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm">
                        Submit Ticket
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection