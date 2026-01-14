<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'AstroMarket') }}</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-50 text-gray-900">
    <div class="min-h-screen flex flex-col">
        <!-- Navigation -->
        <nav class="bg-white border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <a href="{{ route('home') }}" class="text-2xl font-bold text-indigo-600">
                                🌟 AstroMarket
                            </a>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <a href="{{ route('home') }}"
                                class="inline-flex items-center px-1 pt-1 border-b-2 border-indigo-500 text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out">
                                Home
                            </a>
                            <a href="#"
                                class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                Astrologers
                            </a>
                            <a href="#"
                                class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                Shop
                            </a>
                            <a href="#"
                                class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                Live
                            </a>
                        </div>
                    </div>
                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        @auth
                            @if(auth()->user()->role === 'admin')
                                <a href="{{ route('admin.dashboard') }}"
                                    class="text-sm font-medium text-gray-500 hover:text-gray-700">Admin Dashboard</a>
                            @elseif(auth()->user()->role === 'astrologer')
                                <a href="{{ route('astrologer.dashboard') }}"
                                    class="text-sm font-medium text-gray-500 hover:text-gray-700">Astrologer Dashboard</a>
                            @else
                                <a href="{{ route('dashboard') }}"
                                    class="text-sm font-medium text-gray-500 hover:text-gray-700">My Dashboard</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}" class="ml-4">
                                @csrf
                                <button type="submit"
                                    class="text-sm font-medium text-red-600 hover:text-red-900">Logout</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-medium text-gray-500 hover:text-gray-900">Log
                                in</a>
                            <a href="#" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-900">Register</a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="flex-grow">
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-100">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <p class="text-center text-sm text-gray-500">© 2026 AstroMarket. All rights reserved.</p>
            </div>
        </footer>
    </div>
</body>

</html>