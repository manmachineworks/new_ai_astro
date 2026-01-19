@props(['histories'])

<div class="bg-white dark:bg-zinc-900 overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
    {{-- Filters --}}
    <div
        class="px-4 py-3 border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/50 flex flex-wrap items-center justify-between gap-4">
        <h3 class="text-sm font-medium text-zinc-900 dark:text-white">Chat Logs</h3>

        <div class="flex items-center space-x-2">
            <select
                class="block w-full pl-3 pr-10 py-1.5 text-xs border-zinc-300 dark:border-zinc-700 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-md bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100">
                <option>All Astrologers</option>
                {{-- Logic to populate astrologers would go here --}}
            </select>
            <select
                class="block w-full pl-3 pr-10 py-1.5 text-xs border-zinc-300 dark:border-zinc-700 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-md bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100">
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
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">
                        Astrologer</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Last
                        Active</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Sessions
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-right text-xs font-medium text-zinc-500 uppercase tracking-wider">Total
                        Spend</th>
                    <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">Actions</span>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-800">
                @forelse($histories as $history)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    @if($history['astrologer_image'])
                                        <img class="h-10 w-10 rounded-full object-cover"
                                            src="{{ $history['astrologer_image'] }}" alt="">
                                    @else
                                        <div
                                            class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold">
                                            {{ substr($history['astrologer_name'], 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-zinc-900 dark:text-white">
                                        {{ $history['astrologer_name'] }}</div>
                                    <div class="text-xs text-zinc-500">{{ $history['status'] }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500">
                            {{ $history['last_active'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500">
                            {{ $history['session_count'] }} Threads
                        </td>
                        <td
                            class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-zinc-900 dark:text-white">
                            ₹{{ number_format($history['total_spend'], 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('chats.index') }}"
                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">Open
                                Chat</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-sm text-zinc-500">
                            No chat history found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>