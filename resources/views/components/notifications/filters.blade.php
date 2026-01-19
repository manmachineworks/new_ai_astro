@props(['activeFilter'])

<div class="flex space-x-2 overflow-x-auto pb-2 scrollbar-hide">
    <button wire:click="$set('filter', 'all')" @click="filter = 'all'"
        :class="filter === 'all' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300' : 'bg-white text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-700'"
        class="px-4 py-2 rounded-full text-sm font-medium border border-zinc-200 dark:border-zinc-700 transition whitespace-nowrap">
        All
    </button>
    <button wire:click="$set('filter', 'unread')" @click="filter = 'unread'"
        :class="filter === 'unread' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300' : 'bg-white text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-700'"
        class="px-4 py-2 rounded-full text-sm font-medium border border-zinc-200 dark:border-zinc-700 transition whitespace-nowrap">
        Unread
    </button>
    <button wire:click="$set('filter', 'recharge')" @click="filter = 'recharge'"
        :class="filter === 'recharge' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300' : 'bg-white text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-700'"
        class="px-4 py-2 rounded-full text-sm font-medium border border-zinc-200 dark:border-zinc-700 transition whitespace-nowrap">
        Transactions
    </button>
    <button wire:click="$set('filter', 'offer')" @click="filter = 'offer'"
        :class="filter === 'offer' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300' : 'bg-white text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-700'"
        class="px-4 py-2 rounded-full text-sm font-medium border border-zinc-200 dark:border-zinc-700 transition whitespace-nowrap">
        Offers
    </button>
</div>