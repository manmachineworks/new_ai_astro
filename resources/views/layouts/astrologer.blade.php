<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Astrologer Dashboard</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #6366f1;
            --primary-dark: #4f46e5;
            --sidebar-width: 280px;
            --topbar-height: 70px;
            --bg-light: #f8fafc;
            --text-dark: #1e293b;
            --text-muted: #64748b;
        }
        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
        }
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: white;
            border-right: 1px solid #e2e8f0;
            z-index: 1000;
            transition: all 0.3s ease;
        }
        .main-content {
            margin-left: var(--sidebar-width);
            padding-top: var(--topbar-height);
            min-height: 100vh;
            transition: all 0.3s ease;
        }
        .topbar {
            height: var(--topbar-height);
            position: fixed;
            top: 0;
            right: 0;
            left: var(--sidebar-width);
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #e2e8f0;
            z-index: 900;
            transition: all 0.3s ease;
        }
        .nav-link {
            color: var(--text-muted);
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            border-radius: 8px;
            margin: 0 10px;
            transition: all 0.2s;
        }
        .nav-link:hover, .nav-link.active {
            color: var(--primary-color);
            background-color: #eff6ff;
        }
        .nav-link i {
            width: 20px;
            text-align: center;
        }
        .badge-notification {
            font-size: 0.75rem;
            padding: 0.25em 0.6em;
            border-radius: 50%;
        }
        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .main-content {
                margin-left: 0;
            }
            .topbar {
                left: 0;
            }
            .sidebar.show {
                transform: translateX(0);
            }
        }
        /* Cards */
        .card-premium {
            background: white;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            border-radius: 16px;
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Sidebar -->
    <aside class="sidebar d-flex flex-column" id="sidebar">
        <div class="p-4 d-flex align-items-center gap-2">
            <div class="bg-primary text-white p-2 rounded-3">
                <i class="fas fa-star"></i>
            </div>
            <h5 class="mb-0 fw-bold">AstroPanel</h5>
        </div>

        <nav class="nav flex-column flex-grow-1 mt-2 gap-1">
            <a href="{{ route('astrologer.dashboard') }}" class="nav-link {{ request()->routeIs('astrologer.dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i> Dashboard
            </a>
            
            <div class="nav-header px-4 py-2 mt-3 text-uppercase small fw-bold text-muted" style="font-size: 0.7rem;">Communications</div>
            
            <a href="{{ route('astrologer.calls') }}" class="nav-link {{ request()->routeIs('astrologer.calls') ? 'active' : '' }}">
                <i class="fas fa-phone-alt"></i> Calls
                <span class="badge bg-danger ms-auto rounded-pill" style="display:none">2</span> <!-- Dynamic badge placeholder -->
            </a>
            <a href="{{ route('astrologer.chats') }}" class="nav-link {{ request()->routeIs('astrologer.chats') ? 'active' : '' }}">
                <i class="fas fa-comment-dots"></i> Chats
                <span class="badge bg-success ms-auto rounded-pill" style="display:none">5</span>
            </a>
            
            <div class="nav-header px-4 py-2 mt-3 text-uppercase small fw-bold text-muted" style="font-size: 0.7rem;">Management</div>
            
            <a href="{{ route('astrologer.appointments.index') }}" class="nav-link {{ request()->routeIs('astrologer.appointments.*') ? 'active' : '' }}">
                <i class="fas fa-calendar-alt"></i> Appointments
            </a>
            <a href="{{ route('astrologer.services') }}" class="nav-link {{ request()->routeIs('astrologer.services') ? 'active' : '' }}">
                <i class="fas fa-tags"></i> Pricing & Services
            </a>
            <a href="{{ route('astrologer.availability') }}" class="nav-link {{ request()->routeIs('astrologer.availability') ? 'active' : '' }}">
                <i class="fas fa-clock"></i> Schedule
            </a>
            
            <div class="nav-header px-4 py-2 mt-3 text-uppercase small fw-bold text-muted" style="font-size: 0.7rem;">Account</div>
            
            <a href="{{ route('astrologer.earnings') }}" class="nav-link {{ request()->routeIs('astrologer.earnings') ? 'active' : '' }}">
                <i class="fas fa-wallet"></i> Earnings
            </a>
             <a href="{{ route('astrologer.profile') }}" class="nav-link {{ request()->routeIs('astrologer.profile') ? 'active' : '' }}">
                <i class="fas fa-user-circle"></i> Profile
            </a>
        </nav>

        <div class="p-4 border-top">
            <div class="d-flex align-items-center gap-3">
                <img src="{{ auth()->user()->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name) }}" class="rounded-circle" width="40" height="40" alt="Profile">
                <div class="flex-grow-1 overflow-hidden">
                    <div class="fw-bold text-truncate">{{ auth()->user()->name }}</div>
                    <div class="small text-muted text-truncate">Astrologer</div>
                </div>
                <form method="POST" action="{{ route('auth.logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-link text-muted p-0"><i class="fas fa-sign-out-alt"></i></button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Topbar -->
    <header class="topbar d-flex align-items-center px-4 justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-light d-lg-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h5 class="mb-0 fw-bold d-none d-md-block">@yield('page-title', 'Dashboard')</h5>
        </div>

        <div class="d-flex align-items-center gap-3">
            <!-- Global Online Toggle -->
            <div class="d-flex align-items-center gap-2 bg-white px-3 py-2 rounded-pill border shadow-sm">
                <span class="small fw-bold {{ auth()->user()->astrologerProfile->is_enabled ? 'text-success' : 'text-muted' }}">
                    {{ auth()->user()->astrologerProfile->is_enabled ? 'Online' : 'Offline' }}
                </span>
                <div class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" role="switch" id="globalOnlineToggle" 
                        {{ auth()->user()->astrologerProfile->is_enabled ? 'checked' : '' }}>
                </div>
            </div>

            <!-- Notifications -->
            <button class="btn btn-light rounded-circle position-relative">
                <i class="fas fa-bell"></i>
                <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                    <span class="visually-hidden">New alerts</span>
                </span>
            </button>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content p-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });

        // Global Online Toggle Handler
        document.getElementById('globalOnlineToggle')?.addEventListener('change', function(e) {
            const status = e.target.checked ? 'online' : 'offline';
            const label = e.target.closest('.d-flex').querySelector('span'); // The 'Online'/'Offline' text
            
            fetch("{{ route('astrologer.toggle-status') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                body: JSON.stringify({ status: status })
            })
            .then(res => {
                if(res.ok) {
                    label.textContent = status === 'online' ? 'Online' : 'Offline';
                    label.className = `small fw-bold ${status === 'online' ? 'text-success' : 'text-muted'}`;
                    // Optional: Show toast
                } else {
                    e.target.checked = !e.target.checked; // Revert
                    alert('Failed to update status.');
                }
            })
            .catch(err => {
                e.target.checked = !e.target.checked;
                console.error(err);
            });
        });
    </script>
    @include('partials.firebase-scripts')
    @stack('scripts')
</body>
</html>
