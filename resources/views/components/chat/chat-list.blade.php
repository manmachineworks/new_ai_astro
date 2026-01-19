@props(['sessions'])

{{-- Search --}}
<div class="p-4 border-b border-zinc-200 dark:border-zinc-800">
    <div class="relative">
        <input type="text" placeholder="Search chats..."
            class="w-full pl-10 pr-4 py-2 rounded-lg bg-zinc-100 dark:bg-zinc-800 border-none text-sm focus:ring-2 focus:ring-indigo-500 dark:text-white placeholder-zinc-500">
        <svg class="w-5 h-5 text-zinc-400 absolute left-3 top-2.5" fill="none" stroke="currentColor"
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
    </div>
</div>

{{-- List --}}
<div class="flex-1 overflow-y-auto custom-scrollbar">
    @forelse($sessions as $session)
        <a href="#"
            @click.prevent="mobileChatOpen = true; $dispatch('open-chat', { id: {{ $session['id'] }}, name: '{{ $session['astrologer_name'] }}', image: '{{ $session['astrologer_image'] }}' })"
            class="flex items-center p-4 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 cursor-pointer transition border-b border-zinc-100 dark:border-zinc-800/50 {{ $loop->first ? 'bg-indigo-50/50 dark:bg-indigo-900/10' : '' }}">

            <div class="relative flex-shrink-0">
                @if($session['astrologer_image'])
                    <img class="w-12 h-12 rounded-full object-cover" src="{{ $session['astrologer_image'] }}" alt="">
                @else
                    <div
                        class="w-12 h-12 rounded-full bg-indigo-100 dark:bg-zinc-700 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold text-lg">
                        {{ substr($session['astrologer_name'], 0, 1) }}
                    </div>
                @endif
                @if($session['online'] ?? false)
                    <div
                        class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white dark:border-zinc-900 rounded-full">
                    </div>
                @endif
            </div>

            <div class="ml-4 flex-1 min-w-0">
                <div class="flex items-center justify-between mb-1">
                    <h4 class="text-sm font-semibold text-zinc-900 dark:text-white truncate">
                        {{ $session['astrologer_name'] }}</h4>
                    <span
                        class="text-xs text-zinc-500 {{ $session['unread'] > 0 ? 'text-green-600 font-bold' : '' }}">{{ $session['last_message_time'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <p class="text-sm text-zinc-500 truncate dark:text-zinc-400 w-4/5">{{ $session['last_message'] }}</p>
                    @if($session['unread'] > 0)
                        <span
                            class="flex items-center justify-center w-5 h-5 bg-green-500 text-white text-[10px] font-bold rounded-full">
                            {{ $session['unread'] }}
                        </span>
                    @endif
                </div>
            </div>
        </a>
    @empty
        <div class="p-8 text-center">
            <p class="text-zinc-500 text-sm">No conversations yet.</p>
        </div>
    @endforelse
</div>