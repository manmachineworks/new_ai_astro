@extends('layouts.user')

@section('content')
    <div class="d-flex align-items-center justify-content-center min-vh-75">
        <div class="card border-0 shadow-sm text-center p-5" style="max-width: 500px; width: 100%;">
            <div class="card-body">
                @if(request('status') == 'success')
                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4"
                        style="width: 80px; height: 80px;">
                        <i class="bi bi-check-circle-fill fs-1"></i>
                    </div>
                    <h2 class="fw-bold text-dark mb-3">Payment Successful!</h2>
                    <p class="text-muted mb-4">Your wallet has been recharged successfully. You can now continue using our
                        premium services.</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('user.wallet.index') }}" class="btn btn-primary py-2 fw-bold shadow-sm">
                            Back to Wallet
                        </a>
                        <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary py-2">
                            Go to Dashboard
                        </a>
                    </div>
                @else
                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4"
                        style="width: 80px; height: 80px;">
                        <i class="bi bi-x-circle-fill fs-1"></i>
                    </div>
                    <h2 class="fw-bold text-dark mb-3">Payment Failed</h2>
                    <p class="text-muted mb-4">Something went wrong with your transaction. Please check your bank status and try
                        again.</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('user.wallet.recharge') }}" class="btn btn-danger py-2 fw-bold shadow-sm">
                            Try Again
                        </a>
                        <a href="{{ route('user.support.index') }}" class="btn btn-outline-secondary py-2">
                            Contact Support
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

<style>
    .min-vh-75 {
        min-height: 75vh;
    }
</style>