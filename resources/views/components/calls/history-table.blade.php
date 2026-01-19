@props(['calls'])

<div class="bg-white dark:bg-zinc-900 overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
    {{-- Filters --}}
    <div
        class="px-4 py-3 border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/50 flex flex-wrap items-center justify-between gap-4">
        <h3 class="text-sm font-medium text-zinc-900 dark:text-white">Call Logs</h3>

        <div class="flex items-center space-x-2">
            <select
                class="block w-full pl-3 pr-10 py-1.5 text-xs border-zinc-300 dark:border-zinc-700 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-md bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100">
                <option>All Status</option>
                <option>Completed</option>
                <option>Missed</option>
                <option>Failed</option>
            </select>
            <select
                class="block w-full pl-3 pr-10 py-1.5 text-xs border-zinc-300 dark:border-zinc-700 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-md bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100">
                <option>Last 30 Days</option>
                <option>Last 3 Months</option>
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
                        class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Date &
                        Time</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Duration
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Status
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-right text-xs font-medium text-zinc-500 uppercase tracking-wider">Cost
                    </th>
                    <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">Actions</span>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-800">
                @forelse($calls as $call)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    @if($call['astrologer_image'])
                                        <img class="h-10 w-10 rounded-full object-cover" src="{{ $call['astrologer_image'] }}"
                                            alt="">
                                    @else
                                        <div
                                            class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold">
                                            {{ substr($call['astrologer_name'], 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-zinc-900 dark:text-white">
                                        {{ $call['astrologer_name'] }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500">
                            {{ $call['date'] }}<br>
                            <span class="text-xs text-zinc-400">{{ $call['time'] }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500">
                            {{ $call['duration'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($call['status'] === 'completed')
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">Completed</span>
                            @elseif($call['status'] === 'missed')
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">Missed</span>
                            @elseif($call['status'] === 'busy')
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">Busy</span>
                            @endif
                        </td>
                        <td
                            class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-zinc-900 dark:text-white">
                            @if($call['cost'] > 0)
                                -₹{{ $call['cost'] }}
                            @else
                                ₹0
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="#"
                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">Details</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-sm text-zinc-500">
                            No recent calls found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>