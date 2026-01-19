@props(['days' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']])

<div x-data="{ 
    selectedDate: 0, 
    selectedSlot: null,
    slots: [
        ['10:00 AM', '11:00 AM', '2:00 PM', '4:00 PM'], // Day 0
        ['09:00 AM', '12:00 PM', '3:00 PM'],            // Day 1
        ['10:00 AM', '11:30 AM', '5:00 PM', '6:00 PM'], // Day 2
        [],                                             // Day 3 (Unavailable)
        ['08:00 AM', '09:00 AM'],                       // Day 4
        ['2:00 PM', '3:00 PM', '4:00 PM'],              // Day 5
        ['10:00 AM']                                    // Day 6
    ]
}" class="w-full">

    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Select Date</label>

    {{-- Date Tabs --}}
    <div class="flex space-x-2 overflow-x-auto pb-2 mb-4 custom-scrollbar">
        @foreach($days as $index => $day)
            <button @click="selectedDate = {{ $index }}; selectedSlot = null"
                :class="selectedDate === {{ $index }} ? 'bg-indigo-600 text-white shadow-lg ring-2 ring-indigo-300' : 'bg-white dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700 hover:border-indigo-400'"
                class="flex-shrink-0 flex flex-col items-center justify-center w-14 h-16 rounded-xl transition-all duration-200 focus:outline-none">
                <span class="text-xs font-semibold uppercase opacity-80">{{ $day }}</span>
                <span class="text-lg font-bold">{{ now()->addDays($index)->format('d') }}</span>
            </button>
        @endforeach
    </div>

    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Select Time</label>

    {{-- Slots Grid --}}
    <div class="grid grid-cols-3 sm:grid-cols-4 gap-3 min-h-[100px]">
        <template x-for="slot in slots[selectedDate]" :key="slot">
            <button @click="selectedSlot = slot; $dispatch('slot-selected', { date: selectedDate, time: slot })"
                :class="selectedSlot === slot ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-200 border-zinc-200 dark:border-zinc-700 hover:border-indigo-400'"
                class="py-2.5 px-2 rounded-lg text-sm font-medium border text-center transition focus:outline-none shadow-sm"
                x-text="slot"></button>
        </template>

        <div x-show="slots[selectedDate].length === 0"
            class="col-span-full py-8 text-center text-zinc-500 text-sm italic">
            No slots available for this date.
        </div>
    </div>

    {{-- Selection Feedback --}}
    <input type="hidden" name="slot" :value="selectedSlot">
    <div x-show="selectedSlot"
        class="mt-4 p-3 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg text-sm text-indigo-700 dark:text-indigo-300 flex items-center animate-fade-in-up">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span>Selected: <span class="font-bold" x-text="selectedSlot"></span></span>
    </div>

</div>