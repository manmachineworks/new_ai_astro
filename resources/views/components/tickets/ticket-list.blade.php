@props(['tickets'])

<div class="overflow-hidden bg-white dark:bg-zinc-900 shadow sm:rounded-md border border-zinc-200 dark:border-zinc-800">
    <ul role="list" class="divide-y divide-zinc-200 dark:divide-zinc-800">
        @forelse($tickets as $ticket)
            <li class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition duration-150 ease-in-out">
                <a href="{{ route('user.tickets.show', $ticket['id']) }}" class="block p-4 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center text-sm font-medium text-indigo-600 dark:text-indigo-400 truncate">
                            #{{ $ticket['id'] }} - {{ $ticket['subject'] }}
                        </div>
                        <div class="ml-2 flex-shrink-0 flex">
                            @php
                                $statusColors = [
                                    'Open' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                    'Closed' => 'bg-zinc-100 text-zinc-800 dark:bg-zinc-700 dark:text-zinc-300',
                                    'Pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                ];
                                $color = $statusColors[$ticket['status']] ?? $statusColors['Closed'];
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                {{ $ticket['status'] }}
                            </span>
                        </div>
                    </div>
                    <div class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                        <p class="truncate">{{ $ticket['last_message'] }}</p>
                    </div>
                    <div class="mt-2 flex items-center justify-between">
                        <div class="sm:flex">
                            <div class="flex items-center text-sm text-zinc-500 dark:text-zinc-400">
                                <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-zinc-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                    </path>
                                </svg>
                                {{ $ticket['category'] }}
                            </div>
                        </div>
                        <div class="ml-2 flex items-center text-sm text-zinc-500 dark:text-zinc-400">
                            <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-zinc-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Updated {{ $ticket['updated_at'] }}
                        </div>
                    </div>
                </a>
            </li>
        @empty
            <li class="p-12 text-center text-zinc-500">
                <p>No tickets found.</p>
            </li>
        @endforelse
    </ul>
</div>