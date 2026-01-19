@props(['transactions'])

<div class="bg-white dark:bg-zinc-900 overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
    {{-- Filters (Header) --}}
    <div class="px-4 py-3 border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/50 flex flex-wrap items-center justify-between gap-4">
        <h3 class="text-sm font-medium text-zinc-900 dark:text-white">Transaction History</h3>
        
        <div class="flex items-center space-x-2">
            <select class="block w-full pl-3 pr-10 py-1.5 text-xs border-zinc-300 dark:border-zinc-700 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-md bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100">
                <option>All Types</option>
                <option>Recharge</option>
                <option>Call</option>
                <option>Chat</option>
                <option>Refund</option>
            </select>
             <select class="block w-full pl-3 pr-10 py-1.5 text-xs border-zinc-300 dark:border-zinc-700 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-md bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100">
                <option>Last 30 Days</option>
                <option>Last 3 Months</option>
                <option>This Year</option>
            </select>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
            <thead class="bg-zinc-50 dark:bg-zinc-800">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Date & ID</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Description</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Type</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-zinc-500 uppercase tracking-wider">Amount</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-zinc-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-800">
                @forelse($transactions as $txn)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500">
                            {{ $txn['date'] }}
                            <div class="text-xs text-zinc-400">#{{ $txn['id'] }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-white">
                            {{ $txn['description'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500">
                             <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium 
                                {{ $txn['type'] === 'recharge' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : '' }}
                                {{ $txn['type'] === 'call' || $txn['type'] === 'chat' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                                {{ $txn['type'] === 'refund' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' : '' }}
                             ">
                                {{ ucfirst($txn['type']) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold {{ $txn['amount'] > 0 && $txn['type'] === 'recharge' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $txn['amount'] > 0 && $txn['type'] === 'recharge' ? '+' : '-' }}₹{{ abs($txn['amount']) }}
                        </td>
                         <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                             @if($txn['status'] === 'completed')
                                <svg class="w-5 h-5 text-green-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                             @elseif($txn['status'] === 'failed')
                                <svg class="w-5 h-5 text-red-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                             @else
                                <span class="text-yellow-500 text-xs">Pending</span>
                             @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-sm text-zinc-500">
                            No transactions found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
