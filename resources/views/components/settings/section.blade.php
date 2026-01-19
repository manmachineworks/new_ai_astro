@props(['title', 'description' => ''])

<div
    class="bg-white dark:bg-zinc-900 shadow rounded-lg border border-zinc-200 dark:border-zinc-800 mb-6 overflow-hidden">
    <div class="px-4 py-5 sm:px-6 border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/50">
        <h3 class="text-lg leading-6 font-medium text-zinc-900 dark:text-white">
            {{ $title }}
        </h3>
        @if($description)
            <p class="mt-1 max-w-2xl text-sm text-zinc-500 dark:text-zinc-400">
                {{ $description }}
            </p>
        @endif
    </div>
    <div class="px-4 py-5 sm:p-6">
        {{ $slot }}
    </div>
</div>