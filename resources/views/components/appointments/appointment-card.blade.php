@props(['appointment', 'canCancel' => false, 'canReschedule' => false])

<div
    class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-800 p-4 transition hover:shadow-md">
    <div class="flex items-start justify-between">

        {{-- Info --}}
        <div class="flex items-center">
            <div class="flex-shrink-0 relative">
                @if($appointment['astrologer_image'])
                    <img class="h-12 w-12 rounded-full object-cover" src="{{ $appointment['astrologer_image'] }}" alt="">
                @else
                    <div
                        class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-lg">
                        {{ substr($appointment['astrologer_name'], 0, 1) }}
                    </div>
                @endif
            </div>

            <div class="ml-4">
                <h4 class="text-sm font-bold text-zinc-900 dark:text-white">{{ $appointment['astrologer_name'] }}</h4>
                <div class="flex items-center mt-1 text-sm text-zinc-500">
                    <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-zinc-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                    {{ $appointment['date'] }}
                </div>
                <div class="flex items-center mt-1 text-sm text-zinc-500">
                    <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-zinc-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ $appointment['time'] }}
                </div>
            </div>
        </div>

        {{-- Status Chip --}}
        <div>
            @if($appointment['status'] === 'upcoming')
                <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                    Upcoming
                </span>
            @elseif($appointment['status'] === 'completed')
                <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                    Completed
                </span>
            @elseif($appointment['status'] === 'cancelled')
                <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">
                    Cancelled
                </span>
            @endif
        </div>
    </div>

    {{-- Actions --}}
    @if(($canCancel || $canReschedule) && $appointment['status'] === 'upcoming')
        <div class="mt-4 pt-4 border-t border-zinc-100 dark:border-zinc-800 flex justify-end space-x-3">
            @if($canCancel)
                <button class="text-sm text-red-600 hover:text-red-800 font-medium">Cancel</button>
            @endif
            @if($canReschedule)
                <button
                    class="text-sm text-indigo-600 hover:text-indigo-800 font-medium bg-indigo-50 dark:bg-indigo-900/20 px-3 py-1.5 rounded-md transition">Reschedule</button>
            @endif
        </div>
    @endif

    {{-- Rebook --}}
    @if($appointment['status'] === 'completed')
        <div class="mt-4 pt-4 border-t border-zinc-100 dark:border-zinc-800 flex justify-end">
            <a href="{{ route('browse') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">Book Again</a>
        </div>
    @endif
</div>