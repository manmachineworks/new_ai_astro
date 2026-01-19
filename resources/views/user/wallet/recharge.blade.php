@extends('layouts.user')

@section('header')
    <x-ui.page-header title="Recharge Wallet" description="Add money to your wallet to consult with astrologers."
        :breadcrumbs="[['label' => 'Wallet', 'url' => route('user.wallet.index')], ['label' => 'Recharge']]" />
@endsection

@section('content')
    <div class="mx-auto" style="max-width: 600px;">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-5">
                    <p class="text-muted small fw-medium mb-1">Available Balance</p>
                    <h2 class="display-5 fw-bold text-primary mb-0">
                        ₹{{ number_format(auth()->user()->wallet_balance ?? 0, 2) }}</h2>
                </div>

                <form action="{{ route('user.wallet.initiate') }}" method="POST" x-data="{ amount: '' }">
                    @csrf

                    <h6 class="fw-bold text-dark mb-3">Select Amount</h6>
                    <div class="row g-2 mb-4">
                        @foreach([100, 200, 500, 1000, 2000, 5000] as $amt)
                            <div class="col-4">
                                <button type="button" @click="amount = '{{ $amt }}'"
                                    :class="amount == '{{ $amt }}' ? 'btn-primary shadow' : 'btn-outline-primary'"
                                    class="btn w-100 py-3 fw-bold transition">
                                    ₹{{ $amt }}
                                </button>
                            </div>
                        @endforeach
                    </div>

                    <div class="mb-4">
                        <label for="amount" class="form-label small fw-medium text-muted">Or Enter Custom Amount</label>
                        <div class="input-group input-group-lg shadow-sm">
                            <span class="input-group-text bg-white border-end-0">₹</span>
                            <input type="number" name="amount" id="amount" x-model="amount"
                                class="form-control border-start-0 ps-1 fw-bold" placeholder="0.00" min="1" required>
                        </div>
                    </div>

                    <div class="d-grid gap-3">
                        <button type="submit" class="btn btn-success btn-lg py-3 fw-bold shadow-sm transition">
                            <i class="bi bi-shield-check me-2"></i>Proceed to Pay
                        </button>

                        <div class="text-center">
                            <p class="text-muted x-small mb-0">
                                <i class="bi bi-lock-fill me-1"></i>Secure checkout powered by PhonePe
                            </p>
                            <p class="text-muted x-small mt-1 opacity-75">
                                By proceeding, you agree to our <a href="#" class="text-decoration-none">Terms of
                                    Service</a>.
                            </p>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .transition {
            transition: all 0.2s ease;
        }

        .x-small {
            font-size: 0.75rem;
        }
    </style>
@endsection