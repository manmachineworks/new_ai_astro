@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
    <div class="container py-4">
        <div class="row">
            <div class="col-md-3">
                @include('astrologer.dashboard.nav')
            </div>
            <div class="col-md-9">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Edit Profile</h5>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <form action="{{ route('astrologer.profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Display Name</label>
                                    <input type="text" name="display_name" class="form-control"
                                        value="{{ old('display_name', $profile->display_name) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Years of Experience</label>
                                    <input type="number" name="experience_years" class="form-control"
                                        value="{{ old('experience_years', $profile->experience_years) }}" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Bio (Tell users about yourself)</label>
                                <textarea name="bio" class="form-control" rows="5"
                                    required>{{ old('bio', $profile->bio) }}</textarea>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Languages (Comma separated)</label>
                                    <!-- Basic implementation, can be Select2 later -->
                                    <input type="text" name="languages[]" class="form-control"
                                        value="{{ implode(',', $profile->languages ?? []) }}" placeholder="English, Hindi">
                                    <small class="text-muted">Separate by comma (for now, backend expects array but form
                                        logic handles simple casting if middleware exists, else simpler: just 1 input for
                                        demo)</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Skills</label>
                                    <input type="text" name="skills[]" class="form-control"
                                        value="{{ implode(',', $profile->skills ?? []) }}" placeholder="Vedic, Tarot">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Specialties</label>
                                    <input type="text" name="specialties[]" class="form-control"
                                        value="{{ implode(',', $profile->specialties ?? []) }}" placeholder="Love, Career">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Gender</label>
                                    <select name="gender" class="form-select">
                                        <option value="male" {{ $profile->gender == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ $profile->gender == 'female' ? 'selected' : '' }}>Female
                                        </option>
                                        <option value="other" {{ $profile->gender == 'other' ? 'selected' : '' }}>Other
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">DOB</label>
                                    <input type="date" name="dob" class="form-control"
                                        value="{{ $profile->dob ? $profile->dob->format('Y-m-d') : '' }}">
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection