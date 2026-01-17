@extends('admin.layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Earnings & Overview</h1>
                <p class="mt-1 text-sm text-gray-500">Platform financial performance</p>
            </div>
            <div>
                <span class="text-sm text-gray-500">Data based on completed orders</span>
            </div>
        </div>

        <!-- KPI Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Revenue -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Revenue</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">INR
                            {{ number_format($totalRevenue, 2) }}</p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full text-green-600 dark:text-green-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-xs text-green-600 flex items-center">
                        <span class="font-medium">Gross Collection</span>
                    </p>
                </div>
            </div>

            <!-- Payouts -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Paid Out</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">INR
                            {{ number_format($totalPaidOut, 2) }}</p>
                    </div>
                    <div class="p-3 bg-orange-100 dark:bg-orange-900 rounded-full text-orange-600 dark:text-orange-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-xs text-orange-600 flex items-center">
                        <span class="font-medium">Astrologer Withdrawals</span>
                    </p>
                </div>
            </div>

            <!-- Platform Balance -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Net Platform</p>
                        <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400 mt-2">INR
                            {{ number_format($platformBalance, 2) }}</p>
                    </div>
                    <div class="p-3 bg-indigo-100 dark:bg-indigo-900 rounded-full text-indigo-600 dark:text-indigo-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-xs text-indigo-600 flex items-center">
                        <span class="font-medium">Revenue - Payouts</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            <a href="{{ route('admin.reports.revenue') }}"
                class="block p-6 border border-gray-200 dark:border-gray-700 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Detailed Revenue Report &rarr;</h3>
                <p class="text-gray-500 text-sm mt-1">View itemized breakdown of Chat, Call, and Gift revenue.</p>
            </a>
            <a href="{{ route('admin.finance.commissions.index') }}"
                class="block p-6 border border-gray-200 dark:border-gray-700 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Commission Settings &rarr;</h3>
                <p class="text-gray-500 text-sm mt-1">Adjust platform fees and astrologer margins.</p>
            </a>
        </div>
    </div>
@endsection