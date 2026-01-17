@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="fw-bold mb-3">Upgrade to <span class="text-gold">AstroGold</span></h1>
            <p class="text-muted lead">Unlock exclusive benefits, discounts, and priority access.</p>
        </div>

        <div class="row justify-content-center">
            @foreach($plans as $plan)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-lg border-0 cosmic-card {{ $loop->last ? 'border-warning' : '' }}">
                        @if($loop->last) <!-- Highlight highest plan mock logic -->
                            <div class="card-header bg-warning text-dark text-center fw-bold">
                                BEST VALUE
                            </div>
                        @endif
                        <div class="card-body text-center p-4">
                            <h3 class="mb-3">{{ $plan->name }}</h3>
                            <div class="mb-4">
                                <span class="display-4 fw-bold">₹{{ (int) $plan->price_amount }}</span>
                                <span class="text-muted">/ {{ $plan->duration_days }} days</span>
                            </div>

                            <ul class="list-unstyled mb-4 text-start mx-auto" style="max-width: 250px;">
                                @if(($plan->benefits_json['call_discount_percent'] ?? 0) > 0)
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>
                                        {{ $plan->benefits_json['call_discount_percent'] }}% Off on Calls</li>
                                @endif
                                @if(($plan->benefits_json['chat_discount_percent'] ?? 0) > 0)
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>
                                        {{ $plan->benefits_json['chat_discount_percent'] }}% Off on Chats</li>
                                @endif
                                @if(($plan->benefits_json['ai_free_messages'] ?? 0) > 0)
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>
                                        {{ $plan->benefits_json['ai_free_messages'] }} Free AI Messages</li>
                                @endif
                                @if($plan->benefits_json['priority_support'] ?? false)
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Priority Support</li>
                                @endif
                            </ul>

                            <form action="{{ route('memberships.checkout', $plan->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-cosmic btn-lg w-100">
                                    Get Started
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection