@extends('layouts.user')

@section('header')
    <x-ui.page-header title="My Wallet" description="Manage your balance and view transactions." :breadcrumbs="[['label' => 'Dashboard', 'url' => route('user.dashboard')], ['label' => 'Wallet']]" />
@endsection

@section('content')

    <div class="row g-4 mb-5">
        {{-- Balance Card --}}
        <div class="col-lg-4">
            <x-user.wallet-card :balance="$balance" />
        </div>

        {{-- Mini Stats --}}
        <div class="col-lg-8">
            <div class="row g-4">
                <div class="col-sm-6">
                    <x-ui.stat-card title="Total Spends" value="₹{{ number_format($totalSpends, 2) }}">
                        <x-slot:icon>
                            <i class="bi bi-graph-down-arrow fs-5"></i>
                        </x-slot:icon>
                    </x-ui.stat-card>
                </div>

                <div class="col-sm-6">
                    <x-ui.stat-card title="Last Recharge" value="₹{{ number_format($lastRecharge['amount'] ?? 0, 2) }}">
                        <x-slot:icon>
                            <i class="bi bi-wallet-fill fs-5"></i>
                        </x-slot:icon>
                        <div class="mt-2 text-muted small">{{ $lastRecharge['date'] ?? 'N/A' }}</div>
                    </x-ui.stat-card>
                </div>
            </div>
        </div>
    </div>

    {{-- Transactions --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark">Recent Transactions</h5>
            <a href="{{ route('user.wallet.transactions') }}" class="text-decoration-none fw-medium">View All</a>
        </div>
        <div class="card-body p-0">
            <x-ui.table>
                <x-slot:header>
                    <th>Transaction ID</th>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>Status</th>
                </x-slot:header>
                <x-slot:rows>
                    @forelse($transactions as $txn)
                        <tr>
                            <td class="text-secondary small">#{{ $txn['id'] }}</td>
                            <td class="text-secondary small">{{ $txn['date'] }}</td>
                            <td class="fw-medium text-dark">{{ $txn['description'] }}</td>
                            <td class="fw-bold {{ $txn['type'] === 'credit' ? 'text-success' : 'text-danger' }}">
                                {{ $txn['type'] === 'credit' ? '+' : '-' }}₹{{ number_format($txn['amount'], 2) }}
                            </td>
                            <td>
                                <x-ui.badge :color="$txn['status'] === 'success' ? 'success' : ($txn['status'] === 'failed' ? 'danger' : 'warning')" :label="ucfirst($txn['status'])" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <x-ui.empty-state title="No transactions found"
                                    description="Your transaction history will appear here." />
                            </td>
                        </tr>
                    @endforelse
                </x-slot:rows>
            </x-ui.table>
        </div>
    </div>

@endsection