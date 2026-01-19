@extends('admin.layouts.app')

@php
    use Illuminate\Support\Str;
@endphp

@section('title', 'Wallet Ledger')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="fw-bold m-0 text-dark">Wallet Ledger</h2>
            <p class="text-muted mb-0">Track transactions across all wallets with quick filters.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.finance.wallets.export') }}" class="btn btn-outline-secondary rounded-pill">
                <i class="fas fa-file-export me-1"></i> Export CSV
            </a>
            @can('wallet_adjustments')
                <a href="{{ route('admin.wallets.index') }}" class="btn btn-primary rounded-pill">
                    <i class="fas fa-wallet me-1"></i> Adjust Wallets
                </a>
            @endcan
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.finance.wallets.index') }}" class="row g-3">
                <div class="col-md">
                    <label class="form-label small text-muted">User Search</label>
                    <input type="text" name="user_search" value="{{ $filters['user_search'] ?? '' }}" class="form-control form-control-sm" placeholder="Name, Email or Phone">
                </div>
                <div class="col-md">
                    <label class="form-label small text-muted">Type</label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach($typeOptions as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['type'] ?? '') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md">
                    <label class="form-label small text-muted">Source</label>
                    <input type="text" name="source" class="form-control form-control-sm" value="{{ $filters['source'] ?? '' }}" placeholder="webhook, admin, etc.">
                </div>
                <div class="col-md">
                    <label class="form-label small text-muted">Start Date (IST)</label>
                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $filters['start_date'] ?? '' }}">
                </div>
                <div class="col-md">
                    <label class="form-label small text-muted">End Date (IST)</label>
                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $filters['end_date'] ?? '' }}">
                </div>
                <div class="col-md-auto d-flex align-items-end">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Apply</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase">
                    <tr>
                        <th>Date (IST)</th>
                        <th>User</th>
                        <th>Amount</th>
                        <th>Type</th>
                        <th>Source</th>
                        <th>Reference</th>
                        <th>Balance After</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                        @php
                            $user = $transaction->user;
                            $ts = $transaction->created_at?->setTimezone('Asia/Kolkata');
                            $amount = $transaction->type === 'debit' ? -abs($transaction->amount) : abs($transaction->amount);
                            $amountClass = $amount < 0 ? 'text-danger' : 'text-success';
                        @endphp
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $ts?->format('d M Y') }}</div>
                                <div class="text-muted small">{{ $ts?->format('H:i') }}</div>
                            </td>
                            <td>
                                @if($user)
                                    <div class="fw-bold">{{ $user->name }}</div>
                                    <div class="text-muted small">{{ $user->email }} · {{ $user->phone }}</div>
                                @else
                                    <span class="text-muted small">User deleted</span>
                                @endif
                            </td>
                            <td class="fw-bold {{ $amountClass }}">₹ {{ number_format($amount, 2) }}</td>
                            <td>{{ ucfirst($transaction->type) }}</td>
                            <td>{{ $transaction->source ?? '-' }}</td>
                            <td class="small">
                                {{ $transaction->reference_type ?? '-' }}
                                @if($transaction->reference_id)
                                    <div class="text-muted">#{{ $transaction->reference_id }}</div>
                                @endif
                            </td>
                            <td>₹ {{ number_format($transaction->balance_after ?? 0, 2) }}</td>
                            <td>{{ Str::limit($transaction->description ?? '-', 60) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">No transactions match your filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center">
            <div class="text-muted small">Showing {{ $transactions->firstItem() ?? 0 }} to {{ $transactions->lastItem() ?? 0 }} of {{ $transactions->total() }} transactions</div>
            {{ $transactions->withQueryString()->links() }}
        </div>
    </div>
@endsection
