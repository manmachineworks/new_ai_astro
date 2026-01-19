@extends('layouts.user')

@section('header')
    <x-ui.page-header title="Customize Your Feed" description="Help us find the perfect astrologer for you by answering a few questions." :breadcrumbs="[['label' => 'Preferences']]" />
@endsection

@section('content')
<div class="mx-auto" style="max-width: 900px;">
    <div class="card border-0 shadow-sm overflow-hidden rounded-4">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-5">
                <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                    <i class="bi bi-magic fs-2"></i>
                </div>
                <h4 class="fw-bold text-dark mb-1">Tailor Your Experience</h4>
                <p class="text-muted small">Your preferences help us recommend the most suitable experts for your needs.</p>
            </div>

            <form action="{{ route('user.preferences.update') }}" method="POST">
                @csrf

                {{-- Languages --}}
                <div class="mb-5">
                    <label class="form-label fw-bold text-dark mb-3"><i class="bi bi-translate me-2 text-primary"></i>Preferred Languages</label>
                    <div class="row g-3">
                        @foreach($allLanguages as $lang)
                            <div class="col-6 col-md-4">
                                <div class="form-check card shadow-none border p-3 h-100 hover-bg-light transition">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" name="languages[]" 
                                        value="{{ $lang }}" id="lang_{{ $lang }}"
                                        {{ in_array($lang, $preferences->preferred_languages ?? []) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-medium text-dark stretched-link ms-4" for="lang_{{ $lang }}">
                                        {{ $lang }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Specialties --}}
                <div class="mb-5">
                    <label class="form-label fw-bold text-dark mb-3"><i class="bi bi-stars me-2 text-warning"></i>Topics of Interest</label>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($allSpecialties as $skill)
                            <input type="checkbox" class="btn-check" name="specialties[]" 
                                   value="{{ $skill }}" id="skill_{{ $skill }}" autocomplete="off"
                                   {{ in_array($skill, $preferences->preferred_specialties ?? []) ? 'checked' : '' }}>
                            <label class="btn btn-outline-primary rounded-pill px-4 fw-medium transition" for="skill_{{ $skill }}">
                                {{ $skill }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="row g-4 mb-5">
                    {{-- Budget --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark mb-3"><i class="bi bi-currency-rupee me-2 text-success"></i>Budget Range (per min)</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-light border-end-0">Min</span>
                            <input type="number" class="form-control bg-white border-start-0 border-end-0 text-center fw-bold" name="budget_min" 
                                   value="{{ $preferences->preferred_price_range['min'] ?? 0 }}">
                            <span class="input-group-text bg-light border-start-0 border-end-0">-</span>
                            <input type="number" class="form-control bg-white border-start-0 border-end-0 text-center fw-bold" name="budget_max" 
                                   value="{{ $preferences->preferred_price_range['max'] ?? 100 }}">
                            <span class="input-group-text bg-light border-start-0">Max</span>
                        </div>
                    </div>
                    
                    {{-- Zodiac --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark mb-3"><i class="bi bi-moon-stars me-2 text-purple"></i>Your Zodiac Sign</label>
                        <select name="zodiac_sign" class="form-select shadow-sm py-2">
                            <option value="">Select Sign (Optional)</option>
                            @foreach(['Aries','Taurus','Gemini','Cancer','Leo','Virgo','Libra','Scorpio','Sagittarius','Capricorn','Aquarius','Pisces'] as $sign)
                                <option value="{{ $sign }}" {{ ($preferences->zodiac_sign ?? '') == $sign ? 'selected' : '' }}>{{ $sign }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary btn-lg py-3 fw-bold shadow-sm hover-scale transition">
                        Save My Preferences
                    </button>
                    <p class="text-center text-muted small mt-3">You can update these settings at any time to refine your matches.</p>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .transition { transition: all 0.2s ease; }
    .hover-bg-light:hover { background-color: #f8f9fa; }
    .hover-scale:hover { transform: translateY(-2px); }
    .text-purple { color: #6f42c1 !important; }
    .btn-check:checked + .btn-outline-primary {
        box-shadow: 0 4px 10px rgba(var(--bs-primary-rgb), 0.2);
    }
</style>
@endsection
