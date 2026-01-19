@props(['price' => 15])

<div
    class="bg-indigo-50 dark:bg-indigo-900/20 border-b border-indigo-100 dark:border-indigo-800 px-4 py-2 flex items-center justify-between text-xs sm:text-sm">
    <div class="flex items-center text-indigo-700 dark:text-indigo-300">
        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
        </svg>
        <span class="font-medium">AI Astrology Assistance</span>
    </div>
    <div class="flex items-center">
        <span class="text-zinc-500 dark:text-zinc-400 mr-2">Cost per query:</span>
        <span class="font-bold text-zinc-900 dark:text-white">₹{{ $price }}</span>
    </div>
</div>