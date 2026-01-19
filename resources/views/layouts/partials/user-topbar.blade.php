<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top shadow-sm px-4 h-16">
    <div class="container-fluid p-0">

        <!-- Toggle Sidebar (Mobile) -->
        <button class="btn btn-light d-lg-none me-2" type="button" data-bs-toggle="offcanvas"
            data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
            <i class="bi bi-list fs-4"></i>
        </button>

        <!-- Search (Placeholder) -->
        <div class="d-none d-md-block">
            <!-- <input type="text" class="form-control" placeholder="Search..."> -->
        </div>

        <div class="ms-auto d-flex align-items-center gap-3">

            <!-- Wallet Badge -->
            <a href="{{ route('user.wallet.index') }}"
                class="btn btn-outline-primary btn-sm rounded-pill d-flex align-items-center gap-2">
                <i class="bi bi-wallet2"></i>
                <span class="fw-bold">₹{{ auth()->user()->wallet_balance ?? 0 }}</span>
            </a>

            <!-- Notifications -->
            <div class="dropdown">
                <a href="#" class="btn btn-light rounded-circle position-relative p-2" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <i class="bi bi-bell fs-5 text-secondary"></i>
                    <span
                        class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                        <span class="visually-hidden">New alerts</span>
                    </span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="min-width: 250px;">
                    <li>
                        <h6 class="dropdown-header">Notifications</h6>
                    </li>
                    <li><a class="dropdown-item small text-wrap py-2" href="#">New call from Astrologer...</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item text-center small text-primary"
                            href="{{ route('user.notifications.index') }}">View All</a></li>
                </ul>
            </div>

            <!-- User Menu -->
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle hidden-arrow"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name ?? 'User' }}&color=7F9CF5&background=EBF4FF"
                        alt="Avatar" class="rounded-circle" width="36" height="36">
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                    <li>
                        <h6 class="dropdown-header">{{ auth()->user()->name ?? 'User' }}</h6>
                    </li>
                    <li><a class="dropdown-item" href="{{ route('user.profile.index') }}"><i
                                class="bi bi-person me-2"></i>Profile</a></li>
                    <li><a class="dropdown-item" href="{{ route('user.wallet.index') }}"><i
                                class="bi bi-credit-card me-2"></i>Wallet</a></li>
                    <li><a class="dropdown-item" href="{{ route('user.settings.index') }}"><i
                                class="bi bi-gear me-2"></i>Settings</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <form method="POST" action="{{ route('auth.logout') }}">
                            @csrf
                            <button class="dropdown-item text-danger" type="submit"><i
                                    class="bi bi-box-arrow-right me-2"></i>Sign Out</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<!-- Low Balance Banner -->
@if((auth()->user()->wallet_balance ?? 0) < 100)
    <div class="alert alert-warning border-0 rounded-0 mb-0 d-flex justify-content-between align-items-center py-2 px-4 shadow-sm"
        role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
            <span>Low balance (₹{{ auth()->user()->wallet_balance ?? 0 }}). Recharge to continue services.</span>
        </div>
        <a href="{{ route('user.wallet.recharge') }}" class="btn btn-sm btn-warning fw-bold text-dark">Recharge</a>
    </div>
@endif