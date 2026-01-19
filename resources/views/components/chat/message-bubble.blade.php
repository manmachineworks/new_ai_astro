@props(['type' => 'sent', 'text', 'time', 'status' => 'sent'])

<div class="flex w-full {{ $type === 'sent' ? 'justify-end' : 'justify-start' }}">
    <div
        class="max-w-[80%] md:max-w-[60%] {{ $type === 'sent' ? 'bg-indigo-600 text-white rounded-l-2xl rounded-tr-2xl' : 'bg-white dark:bg-zinc-800 dark:text-zinc-100 text-zinc-800 rounded-r-2xl rounded-tl-2xl shadow-sm' }} px-4 py-2 relative group">

        <p class="text-sm leading-relaxed whitespace-pre-line">{{ $text }}</p>

        <div class="flex items-center justify-end space-x-1 mt-1 select-none">
            <span class="text-[10px] {{ $type === 'sent' ? 'text-indigo-200' : 'text-zinc-400' }}">{{ $time }}</span>

            @if($type === 'sent')
                <span>
                    @if($status === 'sent')
                        <svg class="w-3 h-3 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    @elseif($status === 'delivered')
                        <div class="flex">
                            <svg class="w-3 h-3 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <svg class="w-3 h-3 text-indigo-300 -ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    @elseif($status === 'read')
                        <div class="flex">
                            <svg class="w-3 h-3 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <svg class="w-3 h-3 text-blue-300 -ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    @endif
                </span>
            @endif
        </div>

    </div>
</div>