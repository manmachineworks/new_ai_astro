<div class="bg-white dark:bg-zinc-900 rounded-2xl shadow p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Availability</h3>
        <a href="#" class="text-sm text-indigo-600 hover:text-indigo-500">View Full Schedule</a>
    </div>

    {{-- Date Tabs --}}
    <div class="flex space-x-2 overflow-x-auto pb-4 mb-2 scrollbar-hide">
        @foreach(['Today', 'Tomorrow', 'Wed, 20'] as $index => $day)
            <button
                class="flex-shrink-0 px-4 py-2 rounded-lg text-sm font-medium {{ $index === 0 ? 'bg-indigo-600 text-white' : 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300' }}">
                {{ $day }}
            </button>
        @endforeach
    </div>

    {{-- Slots Grid --}}
    <div class="grid grid-cols-3 gap-2">
        @foreach(['10:00 AM', '11:30 AM', '2:00 PM', '4:15 PM', '7:00 PM'] as $slot)
            <button
                class="px-2 py-2 text-xs font-medium border border-zinc-200 dark:border-zinc-700 rounded hover:border-indigo-500 hover:text-indigo-600 dark:text-zinc-300 transition text-center">
                {{ $slot }}
            </button>
        @endforeach
    </div>
</div>