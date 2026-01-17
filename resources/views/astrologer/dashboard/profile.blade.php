@extends('layouts.astrologer')

@section('title', 'My Profile')
@section('page-title', 'Profile Settings')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <form action="{{ route('astrologer.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Profile Header -->
                <div class="card card-premium mb-4">
                    <div class="card-body p-4 text-center">
                        <div class="position-relative d-inline-block mb-3">
                            <img src="{{ $profile->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($profile->display_name) }}"
                                class="rounded-circle shadow-sm border border-3 border-white"
                                style="width: 120px; height: 120px; object-fit: cover;">
                            <label for="photoParams"
                                class="position-absolute bottom-0 end-0 bg-white shadow-sm rounded-circle p-2 cursor-pointer border">
                                <i class="fas fa-camera text-primary"></i>
                                <input type="file" name="profile_photo" id="photoParams" class="d-none" accept="image/*">
                            </label>
                        </div>
                        <h4 class="fw-bold mb-1">{{ $profile->display_name }}</h4>
                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1">
                            {{ $profile->experience_years }} Years Exp
                        </span>

                        @if(!$profile->is_verified)
                            <div class="mt-3">
                                <div class="alert alert-warning d-inline-block py-2 px-3 small border-0 mb-0">
                                    <i class="fas fa-clock me-1"></i> Profile is pending verification
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Basic Details -->
                <div class="card card-premium mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="mb-0 fw-bold"><i class="fas fa-user me-2 text-primary"></i>Basic Information</h6>
                    </div>
                    <div class="card-body p-4 pt-0">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Display Name</label>
                                <input type="text" name="display_name" class="form-control"
                                    value="{{ old('display_name', $profile->display_name) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Gender</label>
                                <select name="gender" class="form-select">
                                    <option value="male" {{ $profile->gender == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ $profile->gender == 'female' ? 'selected' : '' }}>Female
                                    </option>
                                    <option value="other" {{ $profile->gender == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted">Bio</label>
                                <textarea name="bio" class="form-control"
                                    rows="4">{{ old('bio', $profile->bio) }}</textarea>
                                <div class="form-text">Brief introduction visible to customers.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Skills & Expertise -->
                <div class="card card-premium mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="mb-0 fw-bold"><i class="fas fa-sparkles me-2 text-warning"></i>Expertise</h6>
                    </div>
                    <div class="card-body p-4 pt-0">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Experience (Years)</label>
                                <input type="number" name="experience_years" class="form-control"
                                    value="{{ old('experience_years', $profile->experience_years) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Date of Birth</label>
                                <input type="date" name="dob" class="form-control"
                                    value="{{ $profile->dob ? $profile->dob->format('Y-m-d') : '' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Languages</label>
                                <input type="text" name="languages[]" class="form-control"
                                    value="{{ is_array($profile->languages) ? implode(',', $profile->languages) : $profile->languages }}"
                                    placeholder="English, Hindi, etc">
                                <div class="form-text">Comma separated</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Skills</label>
                                <input type="text" name="skills[]" class="form-control"
                                    value="{{ is_array($profile->skills) ? implode(',', $profile->skills) : $profile->skills }}"
                                    placeholder="Vedic, Tarot, Numerology">
                                <div class="form-text">Comma separated</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mb-5">
                    <button type="submit" class="btn btn-primary px-5 rounded-pill shadow-sm">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection