@extends('admin.layouts.app')

@section('title', 'Create Promo Campaign')
@section('page_title', 'Create New Campaign')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Campaign Details</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.promos.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Campaign Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Promo Code <span class="text-danger">*</span></label>
                                    <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                                        value="{{ old('code') }}" placeholder="e.g. WELCOME10" required>
                                    <small class="text-muted">Uppercase, alphanumeric</small>
                                    @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Type <span class="text-danger">*</span></label>
                                    <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                        <option value="discount" {{ old('type') == 'discount' ? 'selected' : '' }}>Discount
                                            (Immediate)</option>
                                        <option value="cashback" {{ old('type') == 'cashback' ? 'selected' : '' }}>Cashback
                                            (After Service)</option>
                                    </select>
                                    @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Discount Type <span class="text-danger">*</span></label>
                                    <select name="discount_type"
                                        class="form-select @error('discount_type') is-invalid @enderror" required>
                                        <option value="percent" {{ old('discount_type') == 'percent' ? 'selected' : '' }}>
                                            Percentage</option>
                                        <option value="flat" {{ old('discount_type') == 'flat' ? 'selected' : '' }}>Flat
                                            Amount</option>
                                    </select>
                                    @error('discount_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Discount Value <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="discount_value"
                                        class="form-control @error('discount_value') is-invalid @enderror"
                                        value="{{ old('discount_value') }}" required>
                                    @error('discount_value')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Applies To</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="applies_to[]" value="recharge"
                                        id="recharge">
                                    <label class="form-check-label" for="recharge">Wallet Recharge</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="applies_to[]" value="call"
                                        id="call">
                                    <label class="form-check-label" for="call">Call Sessions</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="applies_to[]" value="chat"
                                        id="chat">
                                    <label class="form-check-label" for="chat">Chat Sessions</label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Min Transaction Amount</label>
                                    <input type="number" step="0.01" name="min_transaction_amount" class="form-control"
                                        value="{{ old('min_transaction_amount', 0) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Max Discount Cap</label>
                                    <input type="number" step="0.01" name="max_discount_amount" class="form-control"
                                        value="{{ old('max_discount_amount') }}" placeholder="Leave empty for no cap">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Valid From</label>
                                    <input type="datetime-local" name="valid_from" class="form-control"
                                        value="{{ old('valid_from') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Valid Until</label>
                                    <input type="datetime-local" name="valid_until" class="form-control"
                                        value="{{ old('valid_until') }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Max Usage Per User</label>
                                    <input type="number" name="usage_limit_per_user" class="form-control"
                                        value="{{ old('usage_limit_per_user', 1) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Max Total Usage</label>
                                    <input type="number" name="max_total_usage" class="form-control"
                                        value="{{ old('max_total_usage') }}" placeholder="Leave empty for unlimited">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="first_time_only" id="first_time"
                                    value="1" {{ old('first_time_only') ? 'checked' : '' }}>
                                <label class="form-check-label" for="first_time">
                                    First-time users only
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Internal Notes</label>
                            <textarea name="internal_notes" class="form-control"
                                rows="3">{{ old('internal_notes') }}</textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Create Campaign</button>
                            <a href="{{ route('admin.promos.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">💡 Tips</h6>
                    <ul class="small text-muted ps-3">
                        <li>Use descriptive codes (e.g., FIRST10, WELCOME20)</li>
                        <li>Discount = Immediate reduction, Cashback = Credit after service</li>
                        <li>Set max caps to control budget</li>
                        <li>Use date ranges for limited-time offers</li>
                        <li>Test with a high usage limit first</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection