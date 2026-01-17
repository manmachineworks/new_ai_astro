@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <!-- Rechange Section -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-primary text-white text-center py-4 rounded-top-4 border-0">
                    <h5 class="mb-0 text-white-50 small text-uppercase fw-bold">Current Balance</h5>
                    <h1 class="display-4 fw-bold mb-0">₹ {{ number_format((float) auth()->user()->wallet_balance, 2) }}</h1>
                </div>
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="fas fa-plus-circle text-primary me-2"></i>Add Money</h5>
                    
                    <form action="{{ route('wallet.initiate') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label text-muted small fw-bold">Enter Amount (₹)</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-white border-end-0 text-muted">₹</span>
                                <input type="number" name="amount" id="amountInput" class="form-control border-start-0 ps-0 fw-bold text-dark" 
                                       placeholder="500" min="1" required step="1">
                            </div>
                        </div>

                        <!-- Quick Amounts -->
                        <div class="d-flex flex-wrap gap-2 mb-4">
                            @foreach([100, 200, 500, 1000, 2000] as $amt)
                                <button type="button" class="btn btn-outline-secondary rounded-pill fw-bold px-3 py-1 btn-sm amount-pill" 
                                        onclick="document.getElementById('amountInput').value = {{ $amt }}">
                                    + ₹{{ $amt }}
                                </button>
                            @endforeach
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold shadow-sm">
                                Proceed to Pay
                            </button>
                        </div>
                        <div class="text-center mt-3">
                            <span class="small text-muted"><i class="fas fa-shield-alt me-1"></i> Secured by PhonePe</span>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- History Section -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">Recent Transactions</h6>
                    <a href="#" class="small text-decoration-none">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($transactions as $txn)
                            <div class="list-group-item border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                        @if($txn->type == 'credit')
                                            <i class="fas fa-arrow-down text-success"></i>
                                        @else
                                            <i class="fas fa-arrow-up text-danger"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark small">{{ $txn->description ?? ucfirst($txn->type) }}</div>
                                        <div class="small text-muted" style="font-size: 0.75rem;">
                                            {{ $txn->created_at->format('d M, h:i A') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold {{ $txn->type == 'credit' ? 'text-success' : 'text-danger' }}">
                                        {{ $txn->type == 'credit' ? '+' : '-' }} ₹{{ number_format($txn->amount, 2) }}
                                    </div>
                                    <div class="small text-muted" style="font-size: 0.7rem;">{{ $txn->status ?? 'Success' }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted small">
                                No transactions yet.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
