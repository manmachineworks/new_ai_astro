@extends('admin.layouts.app')

@section('content')
    <div x-data="{ adjustModalOpen: false, selectedUser: null, actionType: 'credit' }">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">User Wallets</h1>
                    <p class="mt-1 text-sm text-gray-500">Manage user balances and adjustments</p>
                </div>
                <a href="{{ route('admin.finance.wallets.export') }}"
                    class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                    Export List
                </a>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <form method="GET" action="{{ route('admin.finance.wallets.index') }}"
                    class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search User Name, Phone, Email..."
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                    </div>
                    <div class="w-full md:w-48">
                        <select name="sort"
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                            onchange="this.form.submit()">
                            <option value="">Sort By</option>
                            <option value="balance_high" {{ request('sort') == 'balance_high' ? 'selected' : '' }}>Highest
                                Balance</option>
                            <option value="balance_low" {{ request('sort') == 'balance_low' ? 'selected' : '' }}>Lowest
                                Balance</option>
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
                                <th class="px-6 py-4">User</th>
                                <th class="px-6 py-4">Current Balance</th>
                                <th class="px-6 py-4">Last Transaction</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($users as $user)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-3">
                                            <div
                                                class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-600 dark:text-indigo-300 font-bold">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900 dark:text-gray-100">{{ $user->name }}
                                                </div>
                                                <div class="text-xs text-gray-500">{{ $user->phone }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="font-bold {{ $user->wallet_balance < 50 ? 'text-red-500' : 'text-gray-900 dark:text-white' }}">
                                            INR {{ number_format($user->wallet_balance, 2) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-xs">
                                        @if($user->latestWalletTransaction)
                                            <div>{{ $user->latestWalletTransaction->created_at->format('M d, H:i') }}</div>
                                            <div class="text-gray-500 truncate w-32"
                                                title="{{ $user->latestWalletTransaction->description }}">
                                                {{ $user->latestWalletTransaction->type === 'credit' ? '+' : '-' }}
                                                {{ number_format($user->latestWalletTransaction->amount) }}
                                                ({{ $user->latestWalletTransaction->description }})
                                            </div>
                                        @else
                                            <span class="text-gray-400">No history</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right flex justify-end space-x-2">
                                        <a href="{{ route('admin.finance.wallets.show', $user->id) }}"
                                            class="p-2 text-gray-500 hover:text-indigo-600" title="View History">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                            </svg>
                                        </a>
                                        @can('wallet_adjustments')
                                            <button @click="adjustModalOpen = true; selectedUser = {{ $user->toJson() }}"
                                                class="text-xs px-3 py-1 bg-indigo-100 text-indigo-700 rounded hover:bg-indigo-200 dark:bg-indigo-900 dark:text-indigo-300">
                                                Adjust
                                            </button>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">No users found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $users->links() }}
                </div>
            </div>
        </div>

        <!-- Adjustment Modal -->
        <div x-show="adjustModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div x-show="adjustModalOpen" class="fixed inset-0 bg-black opacity-50" @click="adjustModalOpen = false">
                </div>

                <div x-show="adjustModalOpen"
                    class="relative bg-white dark:bg-gray-800 rounded-xl max-w-md w-full p-6 shadow-2xl transform transition-all">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
                        Adjust Wallet: <span x-text="selectedUser ? selectedUser.name : ''"></span>
                    </h3>

                    <form method="POST"
                        :action="'/admin/finance/wallets/' + (selectedUser ? selectedUser.id : '') + '/adjust'">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Action
                                    Type</label>
                                <div class="mt-2 flex space-x-4">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="type" value="credit" x-model="actionType"
                                            class="text-indigo-600">
                                        <span class="ml-2 text-gray-900 dark:text-gray-100">Credit (Add +)</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="type" value="debit" x-model="actionType"
                                            class="text-red-600">
                                        <span class="ml-2 text-gray-900 dark:text-gray-100">Debit (Deduct -)</span>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Amount
                                    (INR)</label>
                                <input type="number" name="amount" min="1" step="0.01" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reason /
                                    Description</label>
                                <textarea name="description" rows="2" required
                                    placeholder="e.g. Refund for failed call session..."
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" @click="adjustModalOpen = false"
                                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                                Cancel
                            </button>
                            <button type="submit"
                                :class="actionType === 'credit' ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-red-600 hover:bg-red-700'"
                                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white">
                                Confirm Adjustment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection