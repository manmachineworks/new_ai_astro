@props(['report'])

<div
    class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-800 p-4 transition hover:shadow-md flex flex-col h-full">
    <div class="flex-1">
        <div class="flex items-center justify-between mb-2">
            <span
                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300">
                {{ $report['category'] }}
            </span>
            <span class="text-xs text-zinc-500">{{ $report['date'] }}</span>
        </div>
        <h4 class="text-md font-bold text-zinc-900 dark:text-white mb-2">{{ $report['title'] }}</h4>
        <p class="text-sm text-zinc-500 dark:text-zinc-400 line-clamp-2">
            {{ $report['description'] }}
        </p>
    </div>

    <div class="mt-4 pt-4 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between">
        <span class="text-sm font-medium text-zinc-900 dark:text-white">
            @if($report['purchased'])
                Owned
            @else
                ₹{{ $report['price'] }}
            @endif
        </span>

        <div class="flex space-x-2">
            @if($report['purchased'])
                <button class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                        </path>
                    </svg>
                </button>
                <button class="text-indigo-600 hover:text-indigo-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                </button>
            @else
                <button class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">Unlock</button>
            @endif
        </div>
    </div>
</div>