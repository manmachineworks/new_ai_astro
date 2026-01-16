@extends('layouts.app')

@section('content')
    <div class="hero-section text-center py-5">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h1 class="display-3 fw-bold mb-4">
                        Your Destiny, <span class="text-gold">Decoded</span>
                    </h1>
                    <p class="lead text-muted mb-5">
                        Connect with over 1000+ verified Vedic Astrologers, Tarot Readers, and Numerologists via Chat or
                        Call.
                    </p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ route('login') }}" class="btn btn-cosmic btn-lg">Chat with Astrologer</a>
                        <a href="#" class="btn btn-glass btn-lg">Get Daily Horoscope</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats / Trusted By -->
    <div class="py-4 bg-white-05 border-top border-bottom border-white-10">
        <div class="container">
            <div class="row text-center g-4">
                <div class="col-md-3 col-6">
                    <h2 class="fw-bold text-gold">50k+</h2>
                    <p class="text-white-50 small mb-0">Consultations</p>
                </div>
                <div class="col-md-3 col-6">
                    <h2 class="fw-bold text-gold">4.8/5</h2>
                    <p class="text-white-50 small mb-0">User Rating</p>
                </div>
                <div class="col-md-3 col-6">
                    <h2 class="fw-bold text-gold">100+</h2>
                    <p class="text-white-50 small mb-0">Verified Astrologers</p>
                </div>
                <div class="col-md-3 col-6">
                    <h2 class="fw-bold text-gold">24/7</h2>
                    <p class="text-white-50 small mb-0">Live Support</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Astrologers Section -->
    <div class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Meet Our <span class="text-gold">Top Experts</span></h2>
                <p class="text-muted">Available now for instant consultation</p>
            </div>

            <div class="row g-4 justify-content-center">
                @forelse($featuredAstrologers as $astro)
                    <div class="col-lg-4 col-md-6">
                        <div class="glass-card h-100 p-0 overflow-hidden">
                            <div class="p-4 text-center">
                                <div class="position-relative d-inline-block d-flex justify-content-center">
                                    <img src="{{ $astro->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($astro->name) . '&background=FFD700&color=000' }}"
                                        class="rounded-circle border border-2 border-warning mb-3" width="80" height="80">
                                    @if($astro->is_active)
                                        <span
                                            class="position-absolute bottom-0 end-0 bg-success border border-dark rounded-circle p-2"
                                            title="Online"></span>
                                    @else
                                        <span
                                            class="position-absolute bottom-0 end-0 bg-secondary border border-dark rounded-circle p-2"
                                            title="Offline"></span>
                                    @endif
                                </div>
                                <h5>{{ $astro->name }}</h5>
                                <p class="text-gold small mb-2">
                                    {{ implode(', ', array_slice($astro->astrologerProfile->skills ?? [], 0, 3)) }}
                                </p>
                                <p class="small text-muted mb-3 line-clamp-2" style="min-height: 48px">
                                    {{ Str::limit($astro->astrologerProfile->bio, 80) }}
                                </p>

                                <div class="d-flex justify-content-center gap-2">
                                    <span class="badge bg-white-10 text-white border border-white-10">
                                        <i class="fas fa-phone me-1"></i> ₹{{ $astro->astrologerProfile->call_per_minute }}/min
                                    </span>
                                    <span class="badge bg-white-10 text-white border border-white-10">
                                        <i class="fas fa-comment me-1"></i>
                                        ₹{{ $astro->astrologerProfile->chat_per_session }}/min
                                    </span>
                                </div>
                            </div>
                            <div class="card-footer bg-white-05 border-top border-white-10 p-3">
                                <a href="{{ route('astrologers.index') }}" class="btn btn-sm btn-cosmic w-100">Consult Now</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center">
                        <p class="text-muted">No astrologers currently featured.</p>
                    </div>
                @endforelse
            </div>

            <div class="text-center mt-5">
                <a href="{{ route('astrologers.index') }}" class="btn btn-outline-light rounded-pill px-4">View All
                    Astrologers</a>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="features py-5 bg-white-05">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="glass-card text-center h-100">
                        <div class="mb-3">
                            <i class="fa-solid fa-comments fa-3x text-gold"></i>
                        </div>
                        <h4>Private Chat</h4>
                        <p class="text-muted">Chat anonymously with expert astrologers regarding your career, love life, and
                            finances.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="glass-card text-center h-100">
                        <div class="mb-3">
                            <i class="fa-solid fa-phone fa-3x text-gold"></i>
                        </div>
                        <h4>Voice Call</h4>
                        <p class="text-muted">Instant voice connection through our secure bridge. Your number is never
                            shared.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="glass-card text-center h-100">
                        <div class="mb-3">
                            <i class="fa-solid fa-robot fa-3x text-gold"></i>
                        </div>
                        <h4>AI Predictions</h4>
                        <p class="text-muted">Get instant answers from our AI Vedic Assistant powered by real astronomical
                            data.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="cta-section py-5 text-center">
        <div class="container">
            <div class="glass-card p-5">
                <h2 class="mb-3">Start your journey today</h2>
                <p class="mb-4">First chat is free for new users!</p>
                <a href="{{ route('login') }}" class="btn btn-cosmic">Sign Up Now</a>
            </div>
        </div>
    </div>
@endsection