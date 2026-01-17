@extends('admin.layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Payment Orders</h1>
                <p class="mt-1 text-sm text-gray-500">Monitor and manage gateway transactions</p>
            </div>
            <a href="{{ route('admin.finance.payments.export') }}"
                class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                Export CSV
            </a>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <form method="GET" action="{{ route('admin.finance.payments.index') }}" class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search Order ID, Transaction ID, or User..."
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                </div>
                <div class="w-full md:w-48">
                    <select name="status"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                        onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>
                <button type="submit"
                    class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Search</button>
            </form>
        </div>

        <!-- Table -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                    <thead class="bg-gray-50 dark:bg-gray-700 text-xs uppercase font-medium">
                        <tr>
                            <th class="px-6 py-4">Order ID</th>
                            <th class="px-6 py-4">User</th>
                            <th class="px-6 py-4">Amount</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Gateway Ref</th>
                            <th class="px-6 py-4">Date</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($orders as $order)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4 font-mono text-xs">
                                    <span class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">#{{ $order->id }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($order->user)
                                        <div class="font-medium text-gray-900 dark:text-gray-100">{{ $order->user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $order->user->phone }}</div>
                                    @else
                                        <span class="text-red-500">Deleted User</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    INR {{ number_format($order->amount, 2) }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($order->status === 'completed')
                                        <span
                                            class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Completed</span>
                                    @elseif($order->status === 'pending')
                                        <span
                                            class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">Pending</span>
                                    @else
                                        <span
                                            class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">{{ ucfirst($order->status) }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-gray-500">
                                    {{ $order->transaction_id ?? '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $order->created_at->format('M d, H:i') }}
                                </td>
                                <td class="px-6 py-4 text-right flex justify-end space-x-2">
                                    @if($order->status === 'pending')
                                        <form action="{{ route('admin.finance.payments.recheck', $order->id) }}" method="POST"
                                            onsubmit="return confirm('Query gateway for status?');">
                                            @csrf
                                            <button type="submit"
                                                class="text-xs px-3 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-300">
                                                Check Status
                                            </button>
                                        </form>
                                    @endif

                                    @if($order->status === 'completed')
                                        @can('manage_payments')
                                            <form action="{{ route('admin.finance.payments.retry_webhook', $order->id) }}" method="POST"
                                                onsubmit="return confirm('Re-trigger wallet credit logic?');">
                                                @csrf
                                                <button type="submit"
                                                    class="text-xs px-3 py-1 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300">
                                                    Retry
                                                </button>
                                            </form>
                                        @endcan
                                    @endif

                                    <a href="{{ route('admin.finance.payments.show', $order->id) }}"
                                        class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    No payment orders found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
@endsection