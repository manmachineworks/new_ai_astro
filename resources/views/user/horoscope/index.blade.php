@extends('layouts.app')

@section('title', 'Horoscope')

@section('content')
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold text-white">Choose Your Zodiac Sign</h2>
            <p class="text-muted">Discover what the stars have in store for you today.</p>
        </div>

        <div class="row g-4">
            @foreach($signs as $sign)
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="{{ route('user.horoscope.daily', ['sign' => $sign]) }}" class="text-decoration-none">
                        <div class="card h-100 shadow-sm border-0 text-center p-4 hover-lift transition-all">
                            <div class="mb-3">
                                <img src="https://zodiac-signs.netlify.app/assets/icons/{{ $sign }}.png"
                                    alt="{{ ucfirst($sign) }}" style="width: 60px; height: 60px;">
                            </div>
                            <h5 class="fw-bold mb-1">{{ ucfirst($sign) }}</h5>
                            <small class="text-muted">View Daily</small>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <div class="text-center mt-5">
            <a href="{{ route('user.kundli.form') }}" class="btn btn-outline-primary btn-lg rounded-pill px-5">
                <i class="fas fa-scroll me-2"></i> Create Free Kundli
            </a>
        </div>
    </div>

    <style>
        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
        }

        .transition-all {
            transition: all 0.3s ease;
        }
    </style>
@endsection