@props(['notification'])

@php
    $typeStyles = [
        'recharge' => [
            'icon' => 'M13 10V3L4 14h7v7l9-11h-7z',
            'bg' => 'bg-yellow-100 dark:bg-yellow-900/30',
            'text' => 'text-yellow-600 dark:text-yellow-400',
        ],
        'call' => [
            'icon' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z',
            'bg' => 'bg-green-100 dark:bg-green-900/30',
            'text' => 'text-green-600 dark:text-green-400',
        ],
        'offer' => [
            'icon' => 'M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7',
            'bg' => 'bg-purple-100 dark:bg-purple-900/30',
            'text' => 'text-purple-600 dark:text-purple-400',
        ],
        'system' => [
            'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            'bg' => 'bg-blue-100 dark:bg-blue-900/30',
            'text' => 'text-blue-600 dark:text-blue-400',
        ],
    ];

    $style = $typeStyles[$notification['type']] ?? $typeStyles['system'];
    $isUnread = !$notification['read'];
@endphp

<div
    class="flex p-4 border-b border-zinc-100 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition duration-150 {{ $isUnread ? 'bg-indigo-50/50 dark:bg-indigo-900/10' : '' }}">
    <div class="flex-shrink-0 mr-4">
        <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $style['bg'] }} {{ $style['text'] }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $style['icon'] }}"></path>
            </svg>
        </div>
    </div>
    <div class="flex-1 min-w-0">
        <div class="flex justify-between items-start">
            <p class="text-sm font-medium text-zinc-900 dark:text-white truncate">
                {{ $notification['title'] }}
            </p>
            <span class="text-xs text-zinc-500 whitespace-nowrap ml-2">
                {{ $notification['time'] }}
            </span>
        </div>
        <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">
            {{ $notification['message'] }}
        </p>
        @if($notification['action'] ?? false)
            <div class="mt-2">
                <a href="{{ $notification['action_url'] }}"
                    class="text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500">
                    {{ $notification['action_text'] }} &rarr;
                </a>
            </div>
        @endif
    </div>
    @if($isUnread)
        <div class="ml-4 flex-shrink-0 self-center">
            <div class="w-2.5 h-2.5 bg-indigo-600 rounded-full"></div>
        </div>
    @endif
</div>