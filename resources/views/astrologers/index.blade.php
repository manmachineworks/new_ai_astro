@extends('layouts.app')

@section('title', 'Astrologers')

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm border-0 sticky-top" style="top: 80px; z-index: 100;">
                <div class="card-header bg-white border-bottom-0 pt-3">
                    <h5 class="offcanvas-title fw-bold">Filters</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('astrologers.index') }}" method="GET">
                        <!-- Skills -->
                        <div class="mb-3">
                            <label class="form-label fw-medium text-muted small">SKILLS</label>
                            @php $skills = ['Vedic', 'Numerology', 'Tarot', 'Vastu', 'Psychic']; @endphp
                            @foreach($skills as $skill)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="skill[]" value="{{ $skill }}" id="skill{{ $loop->index }}" 
                                    {{ in_array($skill, (array)request('skill')) ? 'checked' : '' }}>
                                <label class="form-check-label" for="skill{{ $loop->index }}">
                                    {{ $skill }}
                                </label>
                            </div>
                            @endforeach
                        </div>

                        <!-- Rating -->
                        <div class="mb-3">
                            <label class="form-label fw-medium text-muted small">RATING</label>
                            <select class="form-select form-select-sm" name="min_rating">
                                <option value="">Any</option>
                                <option value="4.5" {{ request('min_rating') == '4.5' ? 'selected' : '' }}>4.5+</option>
                                <option value="4.0" {{ request('min_rating') == '4.0' ? 'selected' : '' }}>4.0+</option>
                                <option value="3.0" {{ request('min_rating') == '3.0' ? 'selected' : '' }}>3.0+</option>
                            </select>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary rounded-pill">Apply Filters</button>
                            @if(request()->anyFilled(['skill', 'language', 'min_rating']))
                                <a href="{{ route('astrologers.index') }}" class="btn btn-link btn-sm text-muted mt-2">Clear All</a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Directory Grid -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold m-0 text-white">Verified Astrologers</h4>
                <div>
                     <select class="form-select form-select-sm" onchange="window.location.href = this.value">
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'rating_desc']) }}" {{ request('sort') == 'rating_desc' ? 'selected' : '' }}>Top Rated</option>
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_asc']) }}" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                     </select>
                </div>
            </div>

            <div class="row g-4">
                @forelse($astrologers as $astrologer)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0 cosmic-card hover-lift transition-all">
                        <div class="card-body text-center p-4">
                            <div class="position-relative d-inline-block mb-3">
                                <img src="{{ $astrologer->profile_photo_path ?? 'https://ui-avatars.com/api/?name='.urlencode($astrologer->display_name).'&background=6366f1&color=fff' }}" 
                                     class="rounded-circle border border-3 border-white shadow-sm" style="width: 100px; height: 100px; object-fit: cover;">
                                @if($astrologer->is_verified)
                                    <span class="position-absolute bottom-0 end-0 bg-success border border-white rounded-circle p-1" title="Verified">
                                        <i class="fas fa-check text-white small" style="font-size: 0.7rem;"></i>
                                    </span>
                                @endif
                            </div>
                            
                            <h5 class="card-title fw-bold mb-1">{{ $astrologer->display_name }}</h5>
                            <div class="text-warning small mb-2">
                                <i class="fas fa-star"></i> {{ $astrologer->rating_avg }} <span class="text-muted">({{ $astrologer->rating_count }})</span>
                            </div>
                            
                            <p class="text-muted small text-truncate-2 mb-3" style="min-height: 40px;">
                                {{ implode(', ', $astrologer->skills ?? []) }}
                            </p>

                            <div class="d-flex justify-content-between align-items-center bg-light rounded-pill px-3 py-2 mb-3">
                                <div class="small">
                                    <i class="fas fa-phone-alt text-success me-1"></i> ₹{{ (int)$astrologer->call_per_minute }}/min
                                </div>
                                <div class="small border-start ps-3">
                                    <i class="fas fa-comment-alt text-primary me-1"></i> ₹{{ (int)$astrologer->chat_per_session }}/min
                                </div>
                            </div>

                            <a href="{{ route('astrologers.public_show', $astrologer->id) }}" class="btn btn-outline-primary rounded-pill w-100 stretched-link">View Profile</a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <img src="https://cdni.iconscout.com/illustration/premium/thumb/no-data-found-8867280-7265556.png" style="width: 200px; opacity: 0.6;">
                    <h5 class="text-muted mt-3">No astrologers found fitting your criteria.</h5>
                    <a href="{{ route('astrologers.index') }}" class="btn btn-primary btn-sm mt-2">Reset Filters</a>
                </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $astrologers->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
