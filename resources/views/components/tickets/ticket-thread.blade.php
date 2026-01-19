@props(['thread'])

<div class="space-y-6">
    @foreach($thread as $message)
        <div class="flex gap-4 {{ $message['is_user'] ? 'flex-row-reverse' : '' }}">
            <div class="flex-shrink-0">
                @if($message['is_user'])
                    <img src="https://ui-avatars.com/api/?name=User&background=6366f1&color=fff" class="h-10 w-10 rounded-full"
                        alt="">
                @else
                    <div class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center">
                        <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                    </div>
                @endif
            </div>

            <div class="flex flex-col max-w-[80%] {{ $message['is_user'] ? 'items-end' : 'items-start' }}">
                <div
                    class="px-4 py-3 rounded-lg shadow-sm {{ $message['is_user'] ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-white' }}">
                    <p class="text-sm whitespace-pre-wrap">{{ $message['message'] }}</p>
                </div>
                <span class="text-xs text-zinc-500 mt-1">
                    {{ $message['sender'] }} • {{ $message['time'] }}
                </span>
            </div>
        </div>
    @endforeach
</div>