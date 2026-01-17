@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0 cosmic-card">
                <div class="card-body p-4">
                    <h2 class="text-center mb-4"><i class="fas fa-magic me-2 text-warning"></i>Customize Your Feed</h2>
                    <p class="text-center text-muted mb-5">Answer a few questions to help us find the perfect astrologer for you.</p>

                    <form action="{{ route('user.preferences.update') }}" method="POST">
                        @csrf

                        <!-- Languages -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Preferred Languages</label>
                            <div class="row g-2">
                                @foreach($allLanguages as $lang)
                                    <div class="col-6 col-md-4">
                                        <div class="form-check p-2 border rounded hover-bg-light">
                                            <input class="form-check-input ms-1" type="checkbox" name="languages[]" 
                                                value="{{ $lang }}" id="lang_{{ $lang }}"
                                                {{ in_array($lang, $preferences->preferred_languages ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label w-100 ms-2" for="lang_{{ $lang }}">
                                                {{ $lang }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Specialties -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Topics of Interest</label>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($allSpecialties as $skill)
                                    <input type="checkbox" class="btn-check" name="specialties[]" 
                                           value="{{ $skill }}" id="skill_{{ $skill }}" autocomplete="off"
                                           {{ in_array($skill, $preferences->preferred_specialties ?? []) ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary rounded-pill" for="skill_{{ $skill }}">
                                        {{ $skill }}
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Budget -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Budget Range (₹/min)</label>
                            <div class="input-group">
                                <span class="input-group-text">Min</span>
                                <input type="number" class="form-control" name="budget_min" 
                                       value="{{ $preferences->preferred_price_range['min'] ?? 0 }}">
                                <span class="input-group-text">Max</span>
                                <input type="number" class="form-control" name="budget_max" 
                                       value="{{ $preferences->preferred_price_range['max'] ?? 100 }}">
                            </div>
                        </div>
                        
                        <!-- Zodiac -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Your Zodiac Sign (Optional)</label>
                            <select name="zodiac_sign" class="form-select">
                                <option value="">Select Sign</option>
                                @foreach(['Aries','Taurus','Gemini','Cancer','Leo','Virgo','Libra','Scorpio','Sagittarius','Capricorn','Aquarius','Pisces'] as $sign)
                                    <option value="{{ $sign }}" {{ ($preferences->zodiac_sign ?? '') == $sign ? 'selected' : '' }}>{{ $sign }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="d-grid pt-3">
                            <button type="submit" class="btn btn-warning btn-lg text-dark fw-bold">Save Preferences</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
