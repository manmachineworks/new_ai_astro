@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="glass-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-0">Edit Profile</h4>
                        <a href="{{ route('astrologer.dashboard') }}" class="btn btn-sm btn-outline-light">Back to
                            Dashboard</a>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('astrologer.profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <!-- Basic Info -->
                            <div class="col-12">
                                <label class="form-label text-muted">Display Name</label>
                                <input type="text" class="form-control form-control-cosmic" value="{{ $user->name }}"
                                    readonly disabled>
                                <small class="text-muted">Contact admin to change name</small>
                            </div>

                            <div class="col-12">
                                <label class="form-label text-muted">Bio / About Me</label>
                                <textarea name="bio" class="form-control form-control-cosmic" rows="4"
                                    required>{{ $profile->bio }}</textarea>
                            </div>

                            <!-- Professional Info -->
                            <div class="col-md-6">
                                <label class="form-label text-muted">Experience (Years)</label>
                                <input type="number" name="experience_years" class="form-control form-control-cosmic"
                                    value="{{ $profile->experience_years }}" min="0" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted">Languages (Comma separated)</label>
                                <input type="text" name="languages" class="form-control form-control-cosmic"
                                    value="{{ implode(', ', $profile->languages ?? []) }}"
                                    placeholder="English, Hindi, Tamil">
                            </div>

                            <!-- Pricing -->
                            <div class="col-12 mt-4">
                                <h5 class="mb-3 border-bottom border-white-10 pb-2">Pricing Settings</h5>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted">Call Rate (₹/min)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-end-0 text-white">₹</span>
                                    <input type="number" name="call_per_minute"
                                        class="form-control form-control-cosmic border-start-0"
                                        value="{{ $profile->call_per_minute }}" min="1" step="0.01">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted">Chat Rate (₹/min)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-end-0 text-white">₹</span>
                                    <input type="number" name="chat_per_session"
                                        class="form-control form-control-cosmic border-start-0"
                                        value="{{ $profile->chat_per_session }}" min="1" step="0.01">
                                </div>
                            </div>

                            <!-- Submission -->
                            <div class="col-12 mt-4 text-end">
                                <button type="submit" class="btn btn-cosmic px-5">Save Changes</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection