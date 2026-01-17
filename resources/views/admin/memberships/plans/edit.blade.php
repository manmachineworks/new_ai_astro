@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="card shadow">
            <div class="card-header">Edit Membership Plan</div>
            <div class="card-body">
                <form action="{{ route('admin.memberships.plans.update', $plan->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Plan Name</label>
                            <input type="text" name="name" class="form-control" required value="{{ $plan->name }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Price (INR)</label>
                            <input type="number" name="price_amount" class="form-control" required min="0" step="0.01"
                                value="{{ $plan->price_amount }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Duration (Days)</label>
                            <input type="number" name="duration_days" class="form-control" required min="1"
                                value="{{ $plan->duration_days }}">
                        </div>
                    </div>

                    <hr>
                    <h5>Benefits</h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Call Discount (%)</label>
                            <input type="number" name="benefits[call_discount_percent]" class="form-control" min="0"
                                max="100" value="{{ $plan->benefits_json['call_discount_percent'] ?? 0 }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Chat Discount (%)</label>
                            <input type="number" name="benefits[chat_discount_percent]" class="form-control" min="0"
                                max="100" value="{{ $plan->benefits_json['chat_discount_percent'] ?? 0 }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Free AI Messages / Cycle</label>
                            <input type="number" name="benefits[ai_free_messages]" class="form-control" min="0"
                                value="{{ $plan->benefits_json['ai_free_messages'] ?? 0 }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="form-check pt-4">
                                <input type="checkbox" name="benefits[priority_support]" class="form-check-input" value="1"
                                    id="prio" {{ ($plan->benefits_json['priority_support'] ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="prio">Priority Support?</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="active" {{ $plan->status == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $plan->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Plan</button>
                </form>
            </div>
        </div>
    </div>
@endsection