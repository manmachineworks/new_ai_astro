@extends('layouts.app')

@section('title', 'Services & Pricing')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-3">
            @include('astrologer.dashboard.nav')
        </div>
        <div class="col-md-9">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Services & Pricing</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    
                    @if(!$profile->is_verified)
                         <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i> 
                            You cannot enable services until your profile is verified by admin.
                        </div>
                    @endif

                    <form action="{{ route('astrologer.services.update') }}" method="POST">
                        @csrf
                        
                        <!-- Call Service -->
                        <div class="card mb-4 border-light bg-light">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="fw-bold m-0"><i class="fas fa-phone-alt me-2"></i> Audio Call</h6>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_call_enabled" value="1" 
                                            {{ $profile->is_call_enabled ? 'checked' : '' }}
                                            {{ !$profile->is_verified ? 'disabled' : '' }}>
                                        <label class="form-check-label">Enable</label>
                                    </div>
                                </div>
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted">Price per Minute (INR)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">₹</span>
                                            <input type="number" name="call_per_minute" class="form-control" value="{{ (int)$profile->call_per_minute }}" min="1">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Chat Service -->
                        <div class="card mb-4 border-light bg-light">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="fw-bold m-0"><i class="fas fa-comment-dots me-2"></i> Chat Session</h6>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_chat_enabled" value="1" 
                                            {{ $profile->is_chat_enabled ? 'checked' : '' }}
                                            {{ !$profile->is_verified ? 'disabled' : '' }}>
                                        <label class="form-check-label">Enable</label>
                                    </div>
                                </div>
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted">Price per Session/Min (INR)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">₹</span>
                                            <input type="number" name="chat_per_session" class="form-control" value="{{ (int)$profile->chat_per_session }}" min="1">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Save Settings</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
