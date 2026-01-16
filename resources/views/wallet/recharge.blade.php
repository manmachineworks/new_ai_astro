@extends('layouts.app')

@section('title', 'Recharge Wallet')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg border-0 cosmic-card">
                    <div class="card-body p-5">
                        <h3 class="card-title text-center mb-4 text-primary">Add Funds to Wallet</h3>

                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <div class="text-center mb-4">
                            <span class="text-muted">Current Balance</span>
                            <h2 class="fw-bold">₹{{ number_format(auth()->user()->wallet_balance, 2) }}</h2>
                        </div>

                        <form action="{{ route('wallet.recharge.init') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="form-label">Amount (INR)</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-white border-end-0">₹</span>
                                    <input type="number" name="amount" class="form-control border-start-0 ps-0"
                                        placeholder="Enter amount" min="1" required>
                                </div>
                                <div class="form-text mt-2">
                                    Suggested:
                                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill me-1"
                                        onclick="document.querySelector('input[name=amount]').value=100">₹100</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill me-1"
                                        onclick="document.querySelector('input[name=amount]').value=500">₹500</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill"
                                        onclick="document.querySelector('input[name=amount]').value=1000">₹1000</button>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-wallet me-2"></i> Pay with PhonePe
                                </button>
                            </div>
                        </form>

                        <div class="text-center mt-4 text-muted small">
                            <i class="fas fa-lock me-1"></i> Secure Payment Gateway
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection