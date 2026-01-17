@extends('admin.layouts.app')

@section('title', 'Edit ' . $campaign->code)
@section('page_title', 'Edit Campaign: ' . $campaign->code)

@section('content')
    <div class="row">
        <div class="col-md-10 mx-auto">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Edit Campaign</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.promos.update', $campaign) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Campaign Name</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $campaign->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="alert alert-info">
                            <strong>Code:</strong> <code>{{ $campaign->code }}</code> (cannot be changed)
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Type</label>
                                    <select name="type" class="form-select" required>
                                        <option value="discount" {{ $campaign->type == 'discount' ? 'selected' : '' }}>
                                            Discount</option>
                                        <option value="cashback" {{ $campaign->type == 'cashback' ? 'selected' : '' }}>
                                            Cashback</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select" required>
                                        <option value="active" {{ $campaign->status == 'active' ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="paused" {{ $campaign->status == 'paused' ? 'selected' : '' }}>Paused
                                        </option>
                                        <option value="expired" {{ $campaign->status == 'expired' ? 'selected' : '' }}>Expired
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Discount Type</label>
                                    <select name="discount_type" class="form-select" required>
                                        <option value="percent" {{ $campaign->discount_type == 'percent' ? 'selected' : '' }}>
                                            Percentage</option>
                                        <option value="flat" {{ $campaign->discount_type == 'flat' ? 'selected' : '' }}>Flat
                                            Amount</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Discount Value</label>
                                    <input type="number" step="0.01" name="discount_value" class="form-control"
                                        value="{{ old('discount_value', $campaign->discount_value) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Min Transaction Amount</label>
                                    <input type="number" step="0.01" name="min_transaction_amount" class="form-control"
                                        value="{{ old('min_transaction_amount', $campaign->min_transaction_amount) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Max Discount Cap</label>
                                    <input type="number" step="0.01" name="max_discount_amount" class="form-control"
                                        value="{{ old('max_discount_amount', $campaign->max_discount_amount) }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Valid From</label>
                                    <input type="datetime-local" name="valid_from" class="form-control"
                                        value="{{ old('valid_from', $campaign->valid_from?->format('Y-m-d\TH:i')) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Valid Until</label>
                                    <input type="datetime-local" name="valid_until" class="form-control"
                                        value="{{ old('valid_until', $campaign->valid_until?->format('Y-m-d\TH:i')) }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Max Usage Per User</label>
                                    <input type="number" name="usage_limit_per_user" class="form-control"
                                        value="{{ old('usage_limit_per_user', $campaign->usage_limit_per_user) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Max Total Usage</label>
                                    <input type="number" name="max_total_usage" class="form-control"
                                        value="{{ old('max_total_usage', $campaign->max_total_usage) }}">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Internal Notes</label>
                            <textarea name="internal_notes" class="form-control"
                                rows="3">{{ old('internal_notes', $campaign->internal_notes) }}</textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Update Campaign</button>
                            <a href="{{ route('admin.promos.show', $campaign) }}"
                                class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection