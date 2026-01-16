@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <!-- Header & Filters -->
        <div class="text-center mb-5">
            <h2 class="fw-bold mb-3">Find Your Guide</h2>
            <div class="row justify-content-center mb-4">
                <div class="col-md-6">
                    <form action="{{ route('astrologers.index') }}" method="GET">
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0 text-white">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" name="search" class="form-control form-control-cosmic border-start-0 ps-0"
                                placeholder="Search by name..." value="{{ request('search') }}">
                            <button class="btn btn-cosmic rounded-end-pill ms-2" type="submit">Search</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Skills Filter -->
            <div class="d-flex flex-wrap justify-content-center gap-2">
                <a href="{{ route('astrologers.index') }}"
                    class="btn btn-sm {{ !request('skill') ? 'btn-cosmic' : 'btn-glass' }}">All</a>
                @foreach($skills as $skill)
                    <a href="{{ route('astrologers.index', ['skill' => $skill]) }}"
                        class="btn btn-sm {{ request('skill') == $skill ? 'btn-cosmic' : 'btn-glass' }}">
                        {{ $skill }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Grid -->
        <div class="row g-4">
            @forelse($astrologers as $astro)
                <div class="col-md-6 col-lg-3">
                    <div class="glass-card h-100 text-center p-3">
                        <!-- Avatar -->
                        <div class="position-relative d-inline-block mb-3">
                            <img src="{{ $astro->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($astro->name) . '&background=1A1A1A&color=FFD700' }}"
                                class="rounded-circle border border-2 border-warning" width="80" height="80"
                                style="object-fit: cover;">
                            <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-dark rounded-circle">
                                <span class="visually-hidden">Online</span>
                            </span>
                        </div>

                        <!-- Info -->
                        <h5 class="mb-1 text-truncate">{{ $astro->name }}</h5>
                        <p class="small text-gold mb-2">
                            {{ implode(', ', array_slice($astro->astrologerProfile->skills ?? [], 0, 3)) }}
                        </p>
                        <p class="small text-muted text-truncate mb-3">
                            {{ implode(', ', $astro->astrologerProfile->languages ?? ['English']) }}
                        </p>

                        <!-- Pricing -->
                        <div class="d-flex justify-content-center gap-3 mb-3 small">
                            <div>
                                <i class="fas fa-phone-alt text-muted mb-1"></i><br>
                                ₹{{ number_format($astro->astrologerProfile->call_per_minute ?? 0, 0) }}/min
                            </div>
                            <div>
                                <i class="fas fa-comments text-muted mb-1"></i><br>
                                ₹{{ number_format($astro->astrologerProfile->chat_per_session ?? 0, 0) }}/min
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="d-grid gap-2">
                            <button class="btn btn-sm btn-cosmic">
                                <i class="fas fa-phone me-1"></i> Call
                            </button>
                            <button class="btn btn-sm btn-glass">
                                <i class="fas fa-comment me-1"></i> Chat
                            </button>
                            <!-- Profile Link -->
                            <!-- <a href="#" class="btn btn-sm btn-link text-muted" style="text-decoration:none">View Profile</a> -->
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="glass-card d-inline-block px-5">
                        <i class="fas fa-star-half-alt fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No Astrologers Found</h4>
                        <a href="{{ route('astrologers.index') }}" class="btn btn-sm btn-glass mt-2">Clear Filters</a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-5 d-flex justify-content-center">
            {{ $astrologers->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection