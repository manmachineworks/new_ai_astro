<div class="flex h-[calc(100vh-140px)] bg-white dark:bg-zinc-900 overflow-hidden border border-zinc-200 dark:border-zinc-800 rounded-lg shadow-sm"
    x-data="{ mobileChatOpen: false }">
    {{-- Left Sidebar --}}
    <div class="w-full md:w-80 lg:w-96 border-r border-zinc-200 dark:border-zinc-800 flex flex-col"
        :class="mobileChatOpen ? 'hidden md:flex' : 'flex'">

        {{ $sidebar }}

    </div>

    {{-- Main Chat Window --}}
    <div class="flex-1 flex flex-col bg-zinc-50 dark:bg-zinc-900/50 relative"
        :class="mobileChatOpen ? 'flex' : 'hidden md:flex'">

        {{ $slot }}

    </div>
</div>