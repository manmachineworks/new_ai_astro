@props(['user'])

<div x-data="{ activeTab: 'personal' }">
    <div class="border-b border-zinc-200 dark:border-zinc-700 mb-6">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button @click="activeTab = 'personal'"
                :class="activeTab === 'personal' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 hover:border-zinc-300 dark:text-zinc-400 dark:hover:text-zinc-300'"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                Personal Details
            </button>
            <button @click="activeTab = 'astro'"
                :class="activeTab === 'astro' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 hover:border-zinc-300 dark:text-zinc-400 dark:hover:text-zinc-300'"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                Astrology Details
            </button>
        </nav>
    </div>

    {{-- Personal Details Form --}}
    <div x-show="activeTab === 'personal'" class="space-y-6">
        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
            <div class="sm:col-span-3">
                <label for="name" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Full Name</label>
                <div class="mt-1">
                    <input type="text" name="name" id="name" value="{{ $user->name }}"
                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white rounded-md">
                </div>
            </div>

            <div class="sm:col-span-3">
                <label for="gender" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Gender</label>
                <div class="mt-1">
                    <select id="gender" name="gender"
                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white rounded-md">
                        <option>Male</option>
                        <option>Female</option>
                        <option>Other</option>
                    </select>
                </div>
            </div>

            <div class="sm:col-span-6">
                <label for="languages" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Preferred
                    Languages</label>
                <div class="mt-1">
                    <input type="text" name="languages" id="languages" placeholder="English, Hindi"
                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white rounded-md">
                </div>
            </div>
        </div>
        <div class="flex justify-end">
            <button type="button"
                class="bg-indigo-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Save Personal Details
            </button>
        </div>
    </div>

    {{-- Astro Details Form --}}
    <div x-show="activeTab === 'astro'" style="display: none;" class="space-y-6">
        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
            <div class="sm:col-span-3">
                <label for="dob" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Date of
                    Birth</label>
                <div class="mt-1">
                    <input type="date" name="dob" id="dob"
                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white rounded-md">
                </div>
            </div>

            <div class="sm:col-span-3">
                <label for="tob" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Time of
                    Birth</label>
                <div class="mt-1">
                    <input type="time" name="tob" id="tob"
                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white rounded-md">
                </div>
            </div>

            <div class="sm:col-span-6">
                <label for="pob" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Place of
                    Birth</label>
                <div class="mt-1">
                    <input type="text" name="pob" id="pob" placeholder="City, State, Country"
                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white rounded-md">
                </div>
            </div>
        </div>
        <div class="flex justify-end">
            <button type="button"
                class="bg-indigo-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Save Astrology Details
            </button>
        </div>
    </div>
</div>