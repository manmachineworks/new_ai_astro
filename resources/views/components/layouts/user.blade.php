<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-zinc-50 dark:bg-zinc-950">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <!-- Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style>
        /* Custom scrollbar hiding */
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    @livewireStyles
</head>

<body class="h-full font-sans antialiased text-zinc-900 dark:text-zinc-100" x-data="{ sidebarOpen: false }">

    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <x-user.sidebar />

        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Topbar -->
            <x-user.topbar />

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto focus:outline-none p-4 lg:p-8">
                <!-- Overlay for mobile sidebar -->
                <div x-show="sidebarOpen" @click="sidebarOpen = false"
                    x-transition:enter="transition-opacity ease-linear duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="transition-opacity ease-linear duration-300"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-zinc-900/80 z-40 lg:hidden" aria-hidden="true"></div>

                @if (session('success'))
                    <x-ui.alert type="success" :message="session('success')" />
                @endif

                @if (session('error'))
                    <x-ui.alert type="error" :message="session('error')" />
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
</body>

</html>