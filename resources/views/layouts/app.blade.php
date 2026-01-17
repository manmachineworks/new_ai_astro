<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Astro') }}</title>

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#1a1a2e">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-glass fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ url('/') }}">
                <i class="fa-solid fa-moon text-gold me-2"></i>Astro<span class="text-gold">Talk</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto gap-3 align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Astrologers</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('blog.index') }}">Blog</a>
                    </li>
                    @guest
                        <li class="nav-item">
                            <a class="btn btn-cosmic btn-sm px-4" href="{{ route('login') }}">Login</a>
                        </li>
                    @else
                        <li class="nav-item">
                            @if(auth()->user()->activeMembership)
                                <a href="{{ route('memberships.my') }}"
                                    class="badge bg-warning text-dark text-decoration-none me-2 p-2">
                                    <i class="fas fa-crown"></i> {{ auth()->user()->activeMembership->plan->name }}
                                </a>
                            @else
                                <a href="{{ route('memberships.index') }}" class="btn btn-outline-warning btn-sm me-2">
                                    <i class="fas fa-gem"></i> Upgrade
                                </a>
                            @endif
                            <a class="btn btn-glass btn-sm" href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <main class="pt-5 mt-5">
        @yield('content')
    </main>

    @include('layouts.partials.bottom_nav')

    <footer class="mt-5 py-4 border-top" style="border-color: rgba(255,255,255,0.1) !important;">
        <div class="container text-center text-muted">
            <small>&copy; {{ date('Y') }} AstroTalk Clone. All rights reserved.</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/service-worker.js')
                    .then(reg => console.log('SW Registered!', reg))
                    .catch(err => console.log('SW Failed', err));
            });
        }
    </script>
    @include('partials.firebase-scripts')
</body>

</html>