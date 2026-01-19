@extends('layouts.public')

@section('content')

    <!-- Hero Section -->
    <section class="hero-section text-center text-white">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-4">Discover Your Cosmic Path</h1>
                    <p class="lead mb-5 opacity-90">Instant access to verified astrologers for life-changing guidance.
                        Privacy guaranteed.</p>
                    <div class="d-flex gap-3 justify-content-center">
                        <a href="{{ route('user.astrologers.index') }}"
                            class="btn btn-light btn-lg text-primary fw-semibold px-5">Talk to Astrologer</a>
                        @auth
                            <a href="{{ route('user.wallet.index') }}" class="btn btn-outline-light btn-lg px-4">Recharge
                                Wallet</a>
                        @else
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg px-4">Get Started</a>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Trust Badges -->
    <div class="bg-white py-4 border-bottom">
        <div class="container">
            <div class="row text-center g-4 justify-content-center text-secondary">
                <div class="col-6 col-md-3">
                    <div class="d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-shield-check fs-4 text-primary"></i>
                        <span class="fw-medium">100% Secure</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-people-fill fs-4 text-primary"></i>
                        <span class="fw-medium">Verified Astrologers</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-lock-fill fs-4 text-primary"></i>
                        <span class="fw-medium">Private & Confidential</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-headset fs-4 text-primary"></i>
                        <span class="fw-medium">24/7 Support</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Featured Astrologers -->
    <section class="py-5 bg-white">
        <div class="container">
            <x-public.section-header title="Featured Astrologers" subtitle="Top rated experts available for you"
                :center="true" />

            <div class="row g-4">
                @forelse($featuredAstrologers ?? [] as $astrologer)
                    <div class="col-12 col-md-6 col-lg-3">
                        <x-public.astrologer-card :astrologer="$astrologer" />
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <div class="text-muted mb-3"><i class="bi bi-stars fs-1"></i></div>
                        <h5>No astrologers available right now</h5>
                        <p class="text-secondary">Please check back later.</p>
                    </div>
                @endforelse
            </div>

            <div class="text-center mt-5">
                <a href="{{ route('user.astrologers.index') }}" class="btn btn-outline-primary px-5">View All
                    Astrologers</a>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="py-5 bg-light">
        <div class="container">
            <x-public.section-header title="How It Works" :center="true" />

            <div class="row g-4 text-center">
                <div class="col-md-4">
                    <div class="bg-white p-4 rounded-4 shadow-sm h-100">
                        <div class="bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                            style="width: 60px; height: 60px;">
                            <i class="bi bi-search fs-3"></i>
                        </div>
                        <h4>1. Choose Astrologer</h4>
                        <p class="text-secondary">Browse profiles, check ratings and reviews to find your perfect guide.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="bg-white p-4 rounded-4 shadow-sm h-100">
                        <div class="bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                            style="width: 60px; height: 60px;">
                            <i class="bi bi-wallet2 fs-3"></i>
                        </div>
                        <h4>2. Recharge Wallet</h4>
                        <p class="text-secondary">Add money to your wallet securely to start a session.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="bg-white p-4 rounded-4 shadow-sm h-100">
                        <div class="bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                            style="width: 60px; height: 60px;">
                            <i class="bi bi-chat-heart-fill fs-3"></i>
                        </div>
                        <h4>3. Connect & Chat</h4>
                        <p class="text-secondary">Start a call or chat session and get instant answers.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Latest Blogs -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end mb-5">
                <div>
                    <h2 class="fw-bold mb-1">Latest from Blog</h2>
                    <p class="text-secondary mb-0">Insights into your astrological journey</p>
                </div>
                <a href="{{ route('blog.index') }}" class="btn btn-link text-decoration-none fw-medium">View All <i
                        class="bi bi-arrow-right"></i></a>
            </div>

            <div class="row g-4">
                @forelse($latestBlogs ?? [] as $blog)
                    <div class="col-md-4">
                        <x-public.blog-card :blog="$blog" />
                    </div>
                @empty
                    <div class="col-12 text-center text-muted py-4">
                        Writing stars are aligning for our first post...
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="py-5 bg-light">
        <div class="container">
            <x-public.section-header title="Frequently Asked Questions" :center="true" />

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="accordion accordion-flush bg-white rounded shadow-sm" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-medium" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#faq1">
                                    Is my consultation private?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-secondary">
                                    Yes, 100%. Your privacy is our top priority. All calls and chats are completely
                                    confidential and secure.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-medium" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#faq2">
                                    How do I pay for sessions?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-secondary">
                                    You can recharge your wallet using UPI, Credit/Debit cards, or Net Banking. The amount
                                    is deducted per minute as you chat or speak.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-medium" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#faq3">
                                    Can I review my astrologer?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-secondary">
                                    Absolutely! After every session, you can rate and review your experience to help others
                                    finding the right guide.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA -->
    <section class="py-5 bg-primary text-white text-center">
        <div class="container">
            <h2 class="fw-bold mb-3">Ready to find your answers?</h2>
            <p class="lead mb-4 opacity-90">Join thousands of satisfied users today.</p>
            <a href="{{ route('user.astrologers.index') }}" class="btn btn-light btn-lg fw-semibold px-5">Start Consultation
                Today</a>
        </div>
    </section>

@endsection
