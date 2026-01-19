@props(['languages' => [], 'specialities' => []])

<div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg p-4 mb-6 shadow-sm">
    <div class="flex flex-col lg:flex-row lg:items-center lg:space-x-4 space-y-4 lg:space-y-0">
        {{-- Search --}}
        <div class="flex-1 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <input type="text" placeholder="Search by name..."
                class="block w-full pl-10 pr-3 py-2 border border-zinc-300 dark:border-zinc-700 rounded-md leading-5 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 placeholder-zinc-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>

        {{-- Filters --}}
        <div class="flex items-center space-x-2 overflow-x-auto pb-2 lg:pb-0 scrollbar-hide">

            {{-- Language --}}
            <select
                class="block w-32 pl-3 pr-10 py-2 text-base border-zinc-300 dark:border-zinc-700 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100">
                <option value="">Language</option>
                <option>English</option>
                <option>Hindi</option>
                <option>Tamil</option>
            </select>

            {{-- Specialization --}}
            <select
                class="block w-36 pl-3 pr-10 py-2 text-base border-zinc-300 dark:border-zinc-700 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100">
                <option value="">Speciality</option>
                <option>Vedic</option>
                <option>Tarot</option>
                <option>Numerology</option>
            </select>

            {{-- Status --}}
            <select
                class="block w-32 pl-3 pr-10 py-2 text-base border-zinc-300 dark:border-zinc-700 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100">
                <option value="">Status</option>
                <option>Online</option>
                <option>Offline</option>
            </select>

            {{-- Price --}}
            <select
                class="block w-32 pl-3 pr-10 py-2 text-base border-zinc-300 dark:border-zinc-700 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100">
                <option value="">Price</option>
                <option>Low to High</option>
                <option>High to Low</option>
            </select>

        </div>

        {{-- Sort (Desktop right, Mobile stacked) --}}
        <div class="flex-shrink-0">
            <select
                class="block w-full lg:w-40 pl-3 pr-10 py-2 text-base border-zinc-300 dark:border-zinc-700 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100">
                <option value="popularity">Sort by: Popularity</option>
                <option value="rating">Rating: High to Low</option>
                <option value="experience">Experience: High to Low</option>
            </select>
        </div>
    </div>
</div>