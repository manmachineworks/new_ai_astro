<div
    class="h-16 bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between px-4 lg:px-8 z-40 sticky top-0">

    <!-- Mobile Menu Button -->
    <button @click="sidebarOpen = !sidebarOpen"
        class="lg:hidden p-2 rounded-md text-zinc-500 hover:text-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800 dark:text-zinc-400 focus:outline-none">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>

    <!-- Page Title / Breadcrumb (Dynamic) -->
    <div class="flex-1 flex items-center ml-4 lg:ml-0">
        <h1 class="text-lg font-semibold text-zinc-800 dark:text-zinc-100">
            @yield('header')
        </h1>
    </div>

    <!-- Right Actions -->
    <div class="flex items-center space-x-4">

        <!-- Wallet Balance (Simplified) -->
        <a href="{{ route('user.wallet') }}"
            class="hidden md:flex items-center px-3 py-1 bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 rounded-full text-sm font-medium hover:bg-green-100 dark:hover:bg-green-900/40 transition">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                </path>
            </svg>
            ₹{{ number_format(auth()->user()->wallet_balance ?? 0, 2) }}
        </a>

        <!-- Notifications -->
        <button
            class="p-2 rounded-full text-zinc-500 hover:text-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800 dark:text-zinc-400 focus:outline-none relative">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                </path>
            </svg>
            <span
                class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-white dark:border-zinc-900"></span>
        </button>

        <!-- Profile Dropdown -->
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" @click.away="open = false"
                class="flex items-center space-x-2 focus:outline-none">
                <div
                    class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-sm">
                    {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                </div>
                <span
                    class="hidden md:block text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ auth()->user()->name ?? 'User' }}</span>
                <svg class="w-4 h-4 text-zinc-400 hidden md:block" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div x-show="open" x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                class="absolute right-0 mt-2 w-48 bg-white dark:bg-zinc-800 rounded-md shadow-lg py-1 border border-zinc-200 dark:border-zinc-700 z-50">
                <a href="{{ route('user.profile') }}"
                    class="block px-4 py-2 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-700">Your
                    Profile</a>
                <a href="{{ route('user.settings') }}"
                    class="block px-4 py-2 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-700">Settings</a>
                <div class="border-t border-zinc-200 dark:border-zinc-700 my-1"></div>
                <form method="POST" action="{{ route('auth.logout') }}">
                    @csrf
                    <button type="submit"
                        class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-zinc-100 dark:hover:bg-zinc-700">Sign
                        out</button>
                </form>
            </div>
        </div>
    </div>
</div>