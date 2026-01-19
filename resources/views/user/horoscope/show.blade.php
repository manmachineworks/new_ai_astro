@extends('layouts.user')

@section('header')
    <x-ui.page-header title="{{ ucfirst($sign) }} Horoscope" :breadcrumbs="[['label' => 'Horoscope', 'url' => route('user.horoscope.index')], ['label' => ucfirst($sign)]]" />
@endsection

@section('content')
    <div class="row g-4">
        {{-- Main Horoscope Content --}}
        <div class="col-lg-8">
            <x-ui.tabs :tabs="['daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly']" active="daily">
                {{-- Daily Tab --}}
                <div x-show="activeTab === 'daily'" class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
                            <span class="text-muted small fw-medium"><i
                                    class="bi bi-calendar3 me-2"></i>{{ now()->format('l, F j, Y') }}</span>
                            <div class="d-flex gap-2">
                                <span
                                    class="badge rounded-pill bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2">Lucky
                                    Color: Red</span>
                                <span
                                    class="badge rounded-pill bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2">Lucky
                                    Number: 5</span>
                            </div>
                        </div>

                        <div class="prose text-dark">
                            <p class="lead fs-5 text-dark mb-4">
                                Today brings a surge of energy to your professional sector. You may find yourself leading a
                                new
                                project or getting recognition for past efforts.
                            </p>
                            <p class="mb-4 text-muted">
                                However, be mindful of your communication with family members in the evening. Mars' position
                                suggests potential misunderstandings if you're too blunt.
                            </p>

                            <h5 class="fw-bold text-dark mt-4 mb-3"><i class="bi bi-heart-fill text-danger me-2"></i>Love
                            </h5>
                            <p class="text-muted">Singles might meet someone interesting at a social gathering. Couples
                                should plan a date night.</p>

                            <h5 class="fw-bold text-dark mt-4 mb-3"><i
                                    class="bi bi-briefcase-fill text-primary me-2"></i>Career</h5>
                            <p class="text-muted mb-0">Stay focused on long-term goals. Avoid office gossip.</p>
                        </div>
                    </div>
                </div>

                {{-- Weekly Tab (Placeholder) --}}
                <div x-show="activeTab === 'weekly'" style="display: none;" class="card border-0 shadow-sm">
                    <div class="card-body p-5 text-center text-muted">
                        <i class="bi bi-clock-history fs-1 mb-3 d-block"></i>
                        <p>Weekly horoscope content is being prepared by our experts.</p>
                    </div>
                </div>

                {{-- Monthly Tab (Placeholder) --}}
                <div x-show="activeTab === 'monthly'" style="display: none;" class="card border-0 shadow-sm">
                    <div class="card-body p-5 text-center text-muted">
                        <i class="bi bi-clock-history fs-1 mb-3 d-block"></i>
                        <p>Monthly horoscope content is being prepared by our experts.</p>
                    </div>
                </div>
            </x-ui.tabs>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            <div class="d-flex flex-column gap-4">
                {{-- Talk to Astrologer --}}
                <div class="card border-0 bg-primary text-white shadow-sm overflow-hidden">
                    <div class="card-body p-4 text-center">
                        <h5 class="fw-bold mb-2">Need personal guidance?</h5>
                        <p class="opacity-75 small mb-4">Talk to our expert astrologers for a detailed Kundli analysis and
                            remedies.</p>
                        <a href="{{ route('user.astrologers.index') }}" class="btn btn-light fw-bold px-4 py-2 shadow-sm">
                            Consult Now
                        </a>
                    </div>
                </div>

                {{-- Other Signs --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h6 class="card-title fw-bold text-dark mb-4">Check Other Signs</h6>
                        <div class="row g-2">
                            @foreach(['Aries', 'Taurus', 'Gemini', 'Cancer', 'Leo', 'Virgo', 'Libra', 'Scorpio', 'Sagittarius', 'Capricorn', 'Aquarius', 'Pisces'] as $s)
                                @if(strtolower($s) !== strtolower($sign))
                                    <div class="col-4">
                                        <a href="{{ route('user.horoscope.show', strtolower($s)) }}"
                                            class="d-block text-center p-2 border rounded text-decoration-none text-muted small hover-bg-light transition">
                                            {{ $s }}
                                        </a>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .hover-bg-light:hover {
            background-color: var(--bs-light);
            color: var(--bs-primary) !important;
            border-color: var(--bs-primary) !important;
        }

        .transition {
            transition: all 0.2s ease;
        }
    </style>
@endsection