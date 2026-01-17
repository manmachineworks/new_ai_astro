<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Dashboard') | Astro Admin</title>

    <!-- Bootstrap 5 & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bs-body-font-family: 'Outfit', sans-serif;
            --bs-primary: #6366f1;
            /* Modern Indigo */
            --bs-primary-rgb: 99, 102, 241;
            --bs-success: #10b981;
            --bs-info: #3b82f6;
            --bs-warning: #f59e0b;
            --bs-danger: #ef4444;
            --bs-light: #f3f4f6;
            --bs-dark: #111827;
        }

        body {
            background: #f9fafb;
            font-size: 0.9rem;
        }

        /* Sidebar Polish */
        .sidebar {
            min-height: 100vh;
            background: #1a1a2e;
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.02);
            z-index: 1000;
        }

        .sidebar .nav-link {
            color: #9ca3af;
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            margin-bottom: 0.25rem;
            transition: all 0.2s;
            font-weight: 500;
        }

        .sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
        }

        .sidebar .nav-link.active {
            color: #fff;
            background: rgba(79, 70, 229, 0.1);
            /* Indigo tint */
            border-right: 3px solid #4f46e5;
        }

        /* Card Polish */
        .card {
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        /* Button Polish */
        .btn-primary {
            background-color: var(--bs-primary);
            border-color: var(--bs-primary);
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        .btn-primary:hover {
            background-color: #4338ca;
            border-color: #4338ca;
        }

        /* Table Polish */
        .table thead th {
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            color: #6b7280;
            font-weight: 600;
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
            padding-top: 1rem;
            padding-bottom: 1rem;
        }

        .table td {
            vertical-align: middle;
        }

        /* Helpers */
        .avatar-circle {
            border-radius: 50%;
            object-fit: cover;
        }

        .fs-7 {
            font-size: 0.75rem;
        }
    </style>
    @stack('styles')
</head>

<body>
    <div class="container-fluid g-0">
        <div class="row g-0">
            @include('admin.partials.sidebar')
            <main class="col-md-9 ms-sm-auto col-lg-10 d-flex flex-column min-vh-100 bg-light">
                @include('admin.partials.topbar')

                <div class="px-4 py-4 flex-grow-1">
                    @if(request()->routeIs('admin.dashboard'))
                        <!-- Dashboard Header handled in view -->
                    @else
                        <!-- Standard Page Header -->
                        <!-- <div class="d-flex justify-content-between align-items-center mb-4 fade-in">
                                    <h1 class="h4 mb-0 fw-bold text-dark">@yield('page_title')</h1>
                                </div> -->
                    @endif

                    <!-- Alerts -->
                    @if(session('status') || session('success'))
                        <div class="alert alert-success border-0 shadow-sm rounded-4 d-flex align-items-center mb-4">
                            <i class="fas fa-check-circle me-3 fs-4"></i>
                            <div>{{ session('status') ?? session('success') }}</div>
                            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger border-0 shadow-sm rounded-4 d-flex align-items-center mb-4">
                            <i class="fas fa-exclamation-circle me-3 fs-4"></i>
                            <div>{{ session('error') }}</div>
                            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
                            <ul class="mb-0 ps-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @yield('content')
                </div>

                <footer class="bg-white border-top py-3 px-4 text-muted small mt-auto">
                    <div class="d-flex justify-content-between">
                        <span>&copy; {{ date('Y') }} Astro Platform. All rights reserved.</span>
                        <span>v1.2.0 (Admin)</span>
                    </div>
                </footer>
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script>
        // Global Tooltip Initialization
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    </script>
    @stack('scripts')
</body>

</html>