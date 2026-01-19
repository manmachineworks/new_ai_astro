@extends('layouts.user')

@section('header')
    <x-ui.page-header title="Dashboard" description="Welcome back, {{ auth()->user()->name ?? 'User' }}!" />
@endsection

@section('content')

    {{-- 1. Wallet & Stats --}}
    <div class="row g-4 mb-4">
        {{-- Wallet Card --}}
        <div class="col-lg-4">
            <x-user.wallet-card :balance="$walletBalance" />
        </div>

        {{-- Stats / Quick Info --}}
        <div class="col-lg-8">
            <div class="row g-4">
                <div class="col-sm-6">
                    <x-ui.stat-card title="Total Spends" value="₹{{ number_format($totalSpends ?? 12500, 2) }}">
                        <x-slot:icon>
                            <div class="p-3 bg-danger bg-opacity-10 rounded text-danger">
                                <i class="bi bi-graph-up-arrow fs-4"></i>
                            </div>
                        </x-slot:icon>
                    </x-ui.stat-card>
                </div>

                <div class="col-sm-6">
                    <x-ui.stat-card title="Consultations" value="{{ $totalConsultations ?? 12 }}">
                        <x-slot:icon>
                            <div class="p-3 bg-primary bg-opacity-10 rounded text-primary">
                                <i class="bi bi-people-fill fs-4"></i>
                            </div>
                        </x-slot:icon>
                    </x-ui.stat-card>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Quick Actions --}}
    <div class="mb-5">
        <h4 class="fw-bold text-dark mb-4">Quick Actions</h4>
        <x-user.quick-actions />
    </div>

    {{-- 3. Featured Astrologers --}}
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-dark mb-0">Featured Astrologers</h4>
            <a href="{{ route('user.astrologers.index') }}" class="text-decoration-none fw-medium">View All</a>
        </div>

        <div class="row g-4">
            @foreach($featuredAstrologers as $astrologer)
                <div class="col-md-6 col-lg-4">
                    <x-user.astrologer-card :astrologer="$astrologer" />
                </div>
            @endforeach
        </div>
    </div>

    {{-- 4. Recent Activity & AI --}}
    <div class="row g-4">
        {{-- Recent Activity --}}
        <div class="col-lg-6">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold text-dark mb-0">Recent Activity</h4>
                <a href="{{ route('user.calls.index') }}" class="text-decoration-none fw-medium">View History</a>
            </div>
            <div class="card border-0 shadow-sm">
                <div class="list-group list-group-flush">
                    @forelse($recentActivity as $activity)
                        <div class="list-group-item p-3 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                    style="width: 40px; height: 40px; background-color: #f8f9fa;">
                                    @if($activity['type'] === 'call')
                                        <i class="bi bi-telephone-fill text-primary"></i>
                                    @elseif($activity['type'] === 'chat')
                                        <i class="bi bi-chat-dots-fill text-success"></i>
                                    @else
                                        <i class="bi bi-wallet2 text-warning"></i>
                                    @endif
                                </div>
                                <div>
                                    <p class="mb-0 fw-medium text-dark">{{ $activity['description'] }}</p>
                                    <small class="text-muted">{{ $activity['date'] }}</small>
                                </div>
                            </div>
                            <span class="fw-bold text-dark">-₹{{ $activity['amount'] }}</span>
                        </div>
                    @empty
                        <div class="list-group-item p-4 text-center text-muted">No recent activity</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- AI Assistant Teaser --}}
        <div class="col-lg-6">
            <h4 class="fw-bold text-dark mb-4">Ask AI</h4>
            <div class="card border-0 shadow-sm text-white overflow-hidden"
                style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);">
                <div class="card-body p-4 position-relative z-1">
                    <h5 class="fw-bold mb-2">Have a burning question?</h5>
                    <p class="text-white-50 mb-4">Get instant astrological insights powered by AI tailored to your unique
                        birth chart.</p>

                    <a href="{{ route('user.ai.index') }}"
                        class="btn btn-light text-primary fw-semibold d-inline-flex align-items-center">
                        Ask Now
                        <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
                {{-- Decorative Elements --}}
                <i class="bi bi-stars position-absolute bottom-0 end-0 text-white opacity-25"
                    style="font-size: 8rem; margin-right: -20px; margin-bottom: -30px;"></i>
            </div>
        </div>
    </div>

@endsection