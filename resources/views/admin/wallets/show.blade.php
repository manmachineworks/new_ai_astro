@extends('admin.layouts.app')

@section('title', 'Wallet History: ' . $user->name)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold m-0 text-dark">Wallet History</h2>
            <p class="text-muted mb-0">Transactions for <span class="fw-bold text-primary">{{ $user->name }}</span>
                ({{ $user->email }})</p>
        </div>
        <div class="text-end">
            <h3 class="mb-0 fw-bold {{ $user->wallet_balance < 0 ? 'text-danger' : 'text-success' }}">
                ₹{{ number_format($user->wallet_balance, 2) }}
            </h3>
            <small class="text-muted text-uppercase fw-bold">Current Balance</small>
        </div>
    </div>

    <x-admin.table :columns="['Date', 'Type', 'Amount', 'Balance After', 'Reference', 'Description']" :rows="$transactions">
        @forelse($transactions as $txn)
            <tr>
                <td class="ps-4">
                    <div class="fw-bold text-dark">{{ $txn->created_at->format('M d, Y') }}</div>
                    <div class="small text-muted">{{ $txn->created_at->format('H:i:s') }}</div>
                </td>
                <td>
                    @if($txn->type == 'credit')
                        <span class="badge bg-success-subtle text-success rounded-pill px-3"><i class="fas fa-arrow-up me-1"></i>
                            Credit</span>
                    @else
                        <span class="badge bg-danger-subtle text-danger rounded-pill px-3"><i class="fas fa-arrow-down me-1"></i>
                            Debit</span>
                    @endif
                </td>
                <td class="fw-bold {{ $txn->type == 'credit' ? 'text-success' : 'text-danger' }}">
                    {{ $txn->type == 'credit' ? '+' : '-' }}₹{{ number_format(abs($txn->amount), 2) }}
                </td>
                <td class="fw-bold text-dark">
                    ₹{{ number_format($txn->balance_after, 2) }}
                </td>
                <td>
                    <span class="badge bg-light text-dark border">{{ $txn->source ?? 'System' }}</span>
                </td>
                <td class="small text-muted pe-4">
                    {{ $txn->description }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center py-5">
                    <div class="text-muted mb-2"><i class="fas fa-receipt fa-3x opacity-25"></i></div>
                    <p class="text-muted">No transactions found for this user.</p>
                </td>
            </tr>
        @endforelse
    </x-admin.table>
@endsection