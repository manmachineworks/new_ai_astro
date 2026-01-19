@extends('layouts.user')

@section('header')
    <x-ui.page-header title="Transaction History" description="View all your wallet transactions." :breadcrumbs="[['label' => 'Wallet', 'url' => route('user.wallet.index')], ['label' => 'Transactions']]" />
@endsection

@section('content')
    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <x-ui.table>
                <x-slot:header>
                    <th class="ps-4">Transaction ID</th>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th class="pe-4">Status</th>
                </x-slot:header>
                <x-slot:rows>
                    @forelse($transactions as $txn)
                        <tr>
                            <td class="ps-4 text-secondary small">#{{ $txn['id'] }}</td>
                            <td class="text-secondary small">{{ $txn['date'] }}</td>
                            <td class="fw-medium text-dark">{{ $txn['description'] }}</td>
                            <td class="fw-bold {{ ($txn['type'] ?? 'debit') === 'credit' ? 'text-success' : 'text-danger' }}">
                                {{ ($txn['type'] ?? 'debit') === 'credit' ? '+' : '-' }}₹{{ number_format($txn['amount'], 2) }}
                            </td>
                            <td class="pe-4">
                                <x-ui.badge :color="$txn['status'] === 'success' ? 'success' : ($txn['status'] === 'failed' ? 'danger' : 'warning')" :label="ucfirst($txn['status'])" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-5">
                                <x-ui.empty-state title="No transactions found"
                                    description="Your transaction history will appear here." />
                            </td>
                        </tr>
                    @endforelse
                </x-slot:rows>
            </x-ui.table>
        </div>

        @if(method_exists($transactions, 'links'))
            <div class="p-4 border-top">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
@endsection