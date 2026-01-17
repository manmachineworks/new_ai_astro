@extends('layouts.astrologer')

@section('title', 'Services & Pricing')
@section('page-title', 'Services & Pricing')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            @if(!$profile->is_verified)
                <div class="alert alert-warning border-0 shadow-sm rounded-3 mb-4">
                    <i class="fas fa-lock me-2"></i> Your profile is not verified yet. Services will be hidden from the public
                    until verified.
                </div>
            @endif

            <form action="{{ route('astrologer.services.update') }}" method="POST">
                @csrf

                <!-- Call Service -->
                <div class="card card-premium mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-info-subtle p-3 text-info">
                                    <i class="fas fa-phone-alt fa-lg"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-bold">Audio/Video Calls</h5>
                                    <div class="small text-muted">Earn per minute of conversation</div>
                                </div>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_call_enabled"
                                    style="width: 3em; height: 1.5em;" {{ $profile->is_call_enabled ? 'checked' : '' }} {{ !$profile->is_verified ? 'disabled' : '' }}>
                            </div>
                        </div>

                        <div class="bg-light rounded-3 p-3">
                            <label class="form-label small fw-bold text-muted">Rate per Minute (₹)</label>
                            <div class="input-group">
                                <span class="input-group-text border-0 bg-white fw-bold">₹</span>
                                <input type="number" name="call_per_minute" class="form-control border-0 bg-white"
                                    value="{{ $profile->call_per_minute }}" min="0" step="1">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chat Service -->
                <div class="card card-premium mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-success-subtle p-3 text-success">
                                    <i class="fas fa-comments fa-lg"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-bold">Live Chat</h5>
                                    <div class="small text-muted">Earn per message or session</div>
                                </div>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_chat_enabled"
                                    style="width: 3em; height: 1.5em;" {{ $profile->is_chat_enabled ? 'checked' : '' }} {{ !$profile->is_verified ? 'disabled' : '' }}>
                            </div>
                        </div>

                        <div class="bg-light rounded-3 p-3">
                            <label class="form-label small fw-bold text-muted">Rate per Session/Message (₹)</label>
                            <div class="input-group">
                                <span class="input-group-text border-0 bg-white fw-bold">₹</span>
                                <input type="number" name="chat_per_session" class="form-control border-0 bg-white"
                                    value="{{ $profile->chat_per_session ?? 0 }}" min="0" step="1">
                            </div>
                            <div class="form-text mt-2 small">Pricing model: Flat rate per chat session initialization.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary px-5 rounded-pill shadow-sm">
                        Update Services
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection