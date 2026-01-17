@extends('admin.layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Finance Exports</h1>
                <p class="mt-1 text-sm text-gray-500">Download CSV exports for finance operations.</p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <a href="{{ route('admin.finance.payments.export') }}"
                    class="block p-5 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Payments Export</h3>
                    <p class="text-sm text-gray-500 mt-1">Payment orders export (use list filters for scope).</p>
                </a>

                <a href="{{ route('admin.finance.wallets.export') }}"
                    class="block p-5 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Wallet Ledger Export</h3>
                    <p class="text-sm text-gray-500 mt-1">Wallet ledger export (use list filters for scope).</p>
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">More exports</h3>
            <p class="text-sm text-gray-500 mt-1">Refunds, payouts, and earnings exports will appear here when enabled.</p>
        </div>
    </div>
@endsection
