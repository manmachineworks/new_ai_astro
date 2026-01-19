@props(['prompts' => ['Today\'s Horoscope', 'Business Growth', 'Marriage Prospects', 'Health Issue', 'Gemstone Recommendation']])

<div class="flex overflow-x-auto py-2 space-x-2 scrollbar-hide">
    @foreach($prompts as $prompt)
        <button @click="input = '{{ $prompt }}'; $refs.chatInput.focus()"
            class="flex-shrink-0 inline-flex items-center px-3 py-1 rounded-full border border-indigo-200 dark:border-indigo-800 bg-white dark:bg-zinc-800 text-xs font-medium text-indigo-700 dark:text-indigo-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition">
            {{ $prompt }}
        </button>
    @endforeach
</div>