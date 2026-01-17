@extends('layouts.app')

@section('title', $sign . ' ' . $type . ' Horoscope')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('user.horoscope.index') }}"
                                class="text-decoration-none text-muted">Horoscope</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">{{ $sign }}</li>
                    </ol>
                </nav>

                <div class="card shadow border-0 overflow-hidden">
                    <div class="card-header bg-primary text-white p-4 text-center border-0">
                        <img src="https://zodiac-signs.netlify.app/assets/icons/{{ strtolower($sign) }}.png"
                            alt="{{ $sign }}" class="mb-3"
                            style="width: 80px; height: 80px; filter: brightness(0) invert(1);">
                        <h2 class="fw-bold mb-0">{{ $sign }} {{ $type }} Horoscope</h2>
                        <p class="opacity-75 mb-0">{{ $date }}</p>
                    </div>
                    <div class="card-body p-4 p-md-5">
                        <h4 class="fw-bold mb-4">What the stars say:</h4>

                        @if(isset($prediction['prediction']))
                            <div class="fs-5 text-dark lh-base mb-5" style="white-space: pre-wrap;">
                                {{ is_array($prediction['prediction']) ? implode("\n\n", $prediction['prediction']) : $prediction['prediction'] }}
                            </div>
                        @else
                            <div class="alert alert-info border-0 shadow-sm rounded-4">
                                Horoscope details are currently being updated. Please check back shortly.
                            </div>
                        @endif

                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded-4 text-center">
                                    <span class="text-muted small text-uppercase fw-bold d-block mb-1">Lucky Color</span>
                                    <span class="fw-bold fs-5 text-primary">{{ $prediction['color'] ?? 'N/A' }}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded-4 text-center">
                                    <span class="text-muted small text-uppercase fw-bold d-block mb-1">Lucky Number</span>
                                    <span class="fw-bold fs-5 text-primary">{{ $prediction['number'] ?? 'N/A' }}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded-4 text-center">
                                    <span class="text-muted small text-uppercase fw-bold d-block mb-1">Lucky Time</span>
                                    <span class="fw-bold fs-5 text-primary">{{ $prediction['lucky_time'] ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light border-top-0 p-4 text-center">
                        <div class="d-flex justify-content-center gap-3">
                            @if($type === 'Daily')
                                <a href="{{ route('user.horoscope.weekly', ['sign' => strtolower($sign)]) }}"
                                    class="btn btn-outline-primary rounded-pill px-4">View Weekly</a>
                            @else
                                <a href="{{ route('user.horoscope.daily', ['sign' => strtolower($sign)]) }}"
                                    class="btn btn-outline-primary rounded-pill px-4">View Daily</a>
                            @endif
                            <a href="{{ route('user.horoscope.index') }}" class="btn btn-primary rounded-pill px-4">Change
                                Sign</a>
                        </div>
                    </div>
                </div>

                <div class="mt-5 text-center bg-dark p-4 rounded-4 shadow-sm">
                    <h5 class="fw-bold text-white mb-3">Want a more personal reading?</h5>
                    <p class="text-muted small mb-4">Chat with our expert AI Astrologer for deep insights into your birth
                        chart and future.</p>
                    <a href="{{ route('user.ai_chat.index') }}"
                        class="btn btn-warning rounded-pill px-5 fw-bold text-uppercase">
                        <i class="fas fa-robot me-2"></i> Start AI Chat
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection