@extends('layouts.user')

@section('header')
    <x-ui.page-header title="My Profile" description="Manage your personal information and verification." />
@endsection

@section('content')
    <div class="row g-4">
        {{-- Left Column: Forms --}}
        <div class="col-lg-8">
            <div class="d-flex flex-column gap-4">
                {{-- Personal Details --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold text-dark mb-4">Personal Details</h5>
                        <form action="{{ route('user.profile.update') }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-medium text-muted">Full Name</label>
                                    <input type="text" name="name" value="{{ auth()->user()->name }}"
                                        class="form-control" placeholder="Enter your full name">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-medium text-muted">Gender</label>
                                    <select name="gender" class="form-select">
                                        <option value="male" {{ (auth()->user()->profile->gender ?? '') === 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ (auth()->user()->profile->gender ?? '') === 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ (auth()->user()->profile->gender ?? '') === 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-medium text-muted">Date of Birth</label>
                                    <input type="date" name="dob" value="{{ auth()->user()->profile->dob ? (is_string(auth()->user()->profile->dob) ? auth()->user()->profile->dob : auth()->user()->profile->dob->format('Y-m-d')) : '' }}"
                                        class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-medium text-muted">Birth Time</label>
                                    <input type="time" name="tob" value="{{ auth()->user()->profile->meta['tob'] ?? '' }}"
                                        class="form-control">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-medium text-muted">Birth Place</label>
                                    <input type="text" name="pob" value="{{ auth()->user()->profile->location ?? '' }}"
                                        class="form-control" placeholder="City, State, Country">
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary px-4">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Contact Verification --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold text-dark mb-4">Contact Info</h5>
                        <div class="d-flex flex-column gap-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <label class="form-label small fw-medium text-muted mb-0">Phone Number</label>
                                    <div class="fw-bold text-dark">{{ auth()->user()->phone }}</div>
                                </div>
                                <x-ui.badge color="success" label="Verified" />
                            </div>

                            <hr class="my-0">

                            <div x-data="{ editing: false }">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="flex-grow-1 me-3">
                                        <label class="form-label small fw-medium text-muted mb-0">Email Address</label>
                                        <div class="fw-bold text-dark" x-show="!editing">
                                            {{ auth()->user()->email ?? 'Not set' }}
                                        </div>
                                        <div x-show="editing" class="mt-1">
                                            <input type="email" value="{{ auth()->user()->email }}"
                                                class="form-control form-control-sm">
                                        </div>
                                    </div>
                                    <button @click="editing = !editing" class="btn btn-link btn-sm text-primary text-decoration-none fw-medium"
                                        x-text="editing ? 'Cancel' : 'Edit'"></button>
                                </div>
                                <div x-show="editing" class="mt-3 text-end">
                                    <button class="btn btn-primary btn-sm px-3">Update Email</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: KYC --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold text-dark mb-2">KYC Verification</h5>
                    <p class="text-muted small mb-4">Complete your KYC to unlock higher wallet limits and premium features.</p>
                    
                    <div class="d-flex flex-column gap-3">
                        <x-user.profile.kyc-card title="Aadhaar Card" status="approved" />
                        <x-user.profile.kyc-card title="PAN Card" status="pending" />
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection