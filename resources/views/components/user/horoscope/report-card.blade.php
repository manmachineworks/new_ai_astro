@props(['report'])

<div
    class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-800 p-6 flex flex-col h-full">
    <div class="flex-1">
        <div class="flex justify-between items-start">
            <h3 class="text-lg font-bold text-zinc-900 dark:text-white">{{ $report['title'] }}</h3>
            @if($report['purchased'])
                <x-ui.badge color="green" label="Purchased" />
            @else
                <x-ui.badge color="blue" label="Available" />
            @endif
        </div>
        <p class="text-sm text-zinc-500 mt-2">{{ $report['description'] }}</p>
    </div>

    <div class="mt-6 border-t border-zinc-100 dark:border-zinc-800 pt-4">
        @if($report['purchased'])
            <button
                class="w-full flex justify-center items-center py-2 px-4 border border-zinc-300 dark:border-zinc-700 shadow-sm text-sm font-medium rounded-md text-zinc-700 dark:text-zinc-200 bg-white dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-700 transition">
                <svg class="mr-2 h-4 w-4 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Download PDF
            </button>
        @else
            <div class="flex items-center justify-between">
                <span class="text-lg font-bold text-zinc-900 dark:text-white">₹{{ $report['price'] }}</span>
                <button
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Buy Now
                </button>
            </div>
        @endif
    </div>
</div>