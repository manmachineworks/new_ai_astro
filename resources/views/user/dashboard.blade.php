@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row g-4">
            <!-- Sidebar / User Info -->
            <div class="col-lg-4">
                <div class="glass-card mb-4 text-center">
                    <div class="mb-3">
                        <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=FFD700&color=000' }}"
                            class="rounded-circle border border-2 border-warning" width="100" height="100">
                    </div>
                    <h4 class="mb-1">{{ $user->name }}</h4>
                    <p class="text-muted small">{{ $user->email ?? $user->phone }}</p>
                    <form method="POST" action="{{ route('auth.logout') }}">
                        @csrf
                        <button class="btn btn-outline-danger btn-sm rounded-pill px-4">Logout</button>
                    </form>
                </div>

                <!-- Wallet Card -->
                <div class="glass-card bg-gradient-primary">
                    <h6 class="text-uppercase text-muted small mb-3">Wallet Balance</h6>
                    <h2 class="display-5 fw-bold text-gold mb-4">₹ {{ number_format($balance, 2) }}</h2>
                    <div class="d-grid">
                        <button class="btn btn-cosmic" data-bs-toggle="modal" data-bs-target="#rechargeModal">
                            <i class="fas fa-plus-circle me-2"></i> Add Money
                        </button>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Quick Actions -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <a href="#" class="text-decoration-none">
                            <div class="glass-card text-center hover-scale p-3">
                                <i class="fas fa-comments fa-2x text-gold mb-2"></i>
                                <h6 class="mb-0 text-white">Chat Now</h6>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="#" class="text-decoration-none">
                            <div class="glass-card text-center hover-scale p-3">
                                <i class="fas fa-phone-alt fa-2x text-gold mb-2"></i>
                                <h6 class="mb-0 text-white">Call Now</h6>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="#" class="text-decoration-none">
                            <div class="glass-card text-center hover-scale p-3">
                                <i class="fas fa-robot fa-2x text-gold mb-2"></i>
                                <h6 class="mb-0 text-white">Ask AI</h6>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Transaction History -->
                <div class="glass-card">
                    <h5 class="mb-4">Recent Transactions</h5>
                    <div class="table-responsive">
                        <table class="table table-dark table-transparent mb-0">
                            <thead>
                                <tr class="text-muted small text-uppercase">
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $txn)
                                    <tr>
                                        <td class="text-muted small">{{ $txn->created_at->format('M d, h:i A') }}</td>
                                        <td>
                                            <span class="d-block">{{ $txn->description ?? ucfirst($txn->type) }}</span>
                                            <small class="text-muted">{{ $txn->reference_type }}
                                                #{{ $txn->reference_id }}</small>
                                        </td>
                                        <td
                                            class="text-end fw-bold {{ $txn->type == 'credit' ? 'text-success' : 'text-danger' }}">
                                            {{ $txn->type == 'credit' ? '+' : '-' }} ₹{{ number_format($txn->amount, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">No transactions found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $transactions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recharge Modal -->
    <div class="modal fade" id="rechargeModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content glass-card border-0">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Add Money to Wallet</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">Amount (₹)</label>
                        <input type="number" id="rechargeAmount" class="form-control form-control-cosmic form-control-lg"
                            placeholder="100" min="10">
                    </div>
                    <div class="d-grid">
                    <form action="{{ route('wallet.recharge') }}" method="POST">
                        @csrf
                        <input type="hidden" name="amount" id="formAmount">
                        <button type="submit" class="btn btn-cosmic w-100" onclick="document.getElementById('formAmount').value = document.getElementById('rechargeAmount').value">Proceed to Pay</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection