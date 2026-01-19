@extends('layouts.user')

@section('header')
    <x-ui.page-header title="Daily Horoscope" description="Insights for your zodiac sign." />
@endsection

@section('content')
    <div class="row g-4 mb-5">
        @foreach($zodiacSigns as $sign)
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ route('user.horoscope.show', strtolower($sign['name'])) }}" class="text-decoration-none group">
                    <div class="card border-0 shadow-sm h-100 text-center p-4 hover-shadow transition border-hover-primary">
                        <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 70px; height: 70px; font-size: 2rem;">
                            {{ $sign['icon'] }}
                        </div>
                        <h5 class="fw-bold text-dark group-hover-text-primary mb-1">{{ $sign['name'] }}</h5>
                        <p class="text-muted small mb-0">{{ $sign['date_range'] }}</p>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    {{-- Premium Reports Section --}}
    <div class="mt-5">
        <h4 class="fw-bold text-dark mb-4">Premium Reports</h4>
        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <x-user.horoscope.report-card :report="['title' => '2024 Yearly Report', 'description' => 'Detailed analysis of your year ahead based on planetary transits.', 'price' => 499, 'purchased' => false]" />
            </div>
            <div class="col-md-6 col-lg-4">
                <x-user.horoscope.report-card :report="['title' => 'Love & Compatibility', 'description' => 'In-depth look at your relationships and compatibility matches.', 'price' => 299, 'purchased' => false]" />
            </div>
            <div class="col-md-6 col-lg-4">
                <x-user.horoscope.report-card :report="['title' => 'Career & Wealth', 'description' => 'Unlock your financial potential and career growth opportunities.', 'price' => 399, 'purchased' => true]" />
            </div>
        </div>
    </div>

    <style>
        .hover-shadow:hover {
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.1) !important;
            transform: translateY(-5px);
        }
        .transition {
            transition: all 0.3s ease;
        }
        .border-hover-primary:hover {
            border: 1px solid var(--bs-primary) !important;
        }
        .group:hover .group-hover-text-primary {
            color: var(--bs-primary) !important;
        }
    </style>
@endsection