<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - Astrologer</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 font-sans antialiased">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div class="hidden md:flex md:flex-shrink-0">
            <div class="flex flex-col w-64 bg-purple-900">
                <div class="flex items-center h-16 flex-shrink-0 px-4 bg-purple-800 text-white font-bold text-xl">
                    Astrologer Panel
                </div>
                <div class="flex-1 flex flex-col overflow-y-auto">
                    <nav class="flex-1 px-2 py-4 space-y-1">
                        <a href="{{ route('astrologer.dashboard') }}"
                            class="group flex items-center px-2 py-2 text-sm leading-5 font-medium text-white rounded-md bg-purple-800 focus:outline-none focus:bg-purple-700 transition ease-in-out duration-150">
                            Dashboard
                        </a>
                        <a href="{{ route('astrologer.schedule') }}"
                            class="group flex items-center px-2 py-2 text-sm leading-5 font-medium text-purple-300 rounded-md hover:text-white hover:bg-purple-700 focus:outline-none focus:text-white focus:bg-purple-700 transition ease-in-out duration-150">
                            My Schedule
                        </a>
                        <!-- Add more links here -->
                    </nav>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex flex-col w-0 flex-1 overflow-hidden">
            <div class="relative z-10 flex-shrink-0 flex h-16 bg-white shadow">
                <div class="flex-1 px-4 flex justify-between">
                    <div class="flex-1 flex"></div>
                    <div class="ml-4 flex items-center md:ml-6">
                        <span class="text-gray-700 mr-4">{{ Auth::user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="text-sm font-medium text-purple-600 hover:text-purple-900">Logout</button>
                        </form>
                    </div>
                </div>
            </div>

            <main class="flex-1 relative overflow-y-auto focus:outline-none">
                <div class="py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                        {{ $slot }}
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>

</html>