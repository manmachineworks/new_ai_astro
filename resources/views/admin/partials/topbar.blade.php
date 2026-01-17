<header class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm px-4 py-2 border-bottom">
    <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse"
        data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Global Search -->
    <div class="d-none d-md-flex align-items-center flex-grow-1 me-4">
        <form class="position-relative w-100" style="max-width: 400px;" action="{{ route('admin.users.index') }}"
            method="GET">
            <span class="position-absolute top-50 start-0 translate-middle-y ps-3 text-muted">
                <i class="fas fa-search"></i>
            </span>
            <input class="form-control rounded-pill bg-light border-0 ps-5" type="text" name="search"
                placeholder="Search Users, Astrologers..." aria-label="Search">
        </form>
    </div>

    <div class="navbar-nav align-items-center ms-auto gap-3">
        <!-- Global Date Range (Visual Only - Persistent State Requires JS/Session) -->
        <div class="d-none d-lg-flex align-items-center bg-light rounded-pill px-3 py-1 border">
            <i class="far fa-calendar-alt text-muted me-2"></i>
            <span class="small text-muted fw-bold">{{ now()->subDays(7)->format('d M') }} -
                {{ now()->format('d M') }}</span>
        </div>

        <!-- Notification Bell -->
        <div class="dropdown">
            <a class="nav-link position-relative p-2" href="#" id="notificationsDropdown" role="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="far fa-bell fa-lg text-secondary"></i>
                <span
                    class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                    <span class="visually-hidden">New alerts</span>
                </span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-4 p-0"
                aria-labelledby="notificationsDropdown" style="width: 320px;">
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold">Notifications</h6>
                    <small class="text-primary fw-bold" style="cursor: pointer;">Mark all read</small>
                </div>
                <div class="list-group list-group-flush" style="max-height: 300px; overflow-y: auto;">
                    <!-- Mock Notifications -->
                    <a href="{{ route('admin.system.webhooks.index', ['status' => 'failed']) }}"
                        class="list-group-item list-group-item-action p-3 bg-light border-bottom">
                        <div class="d-flex align-items-start">
                            <div class="bg-danger-subtle text-danger rounded-circle p-2 me-3"><i
                                    class="fas fa-exclamation-triangle"></i></div>
                            <div>
                                <div class="small fw-bold text-dark">Webhook Failure</div>
                                <div class="small text-muted">Stripe payment failed signature check.</div>
                                <div class="x-small text-muted mt-1">10 mins ago</div>
                            </div>
                        </div>
                    </a>
                    <a href="{{ route('admin.astrologers.index', ['status' => 'pending']) }}"
                        class="list-group-item list-group-item-action p-3 border-bottom">
                        <div class="d-flex align-items-start">
                            <div class="bg-primary-subtle text-primary rounded-circle p-2 me-3"><i
                                    class="fas fa-user-plus"></i></div>
                            <div>
                                <div class="small fw-bold text-dark">New Verification</div>
                                <div class="small text-muted">Astrologer "Rahul Vedic" requested approval.</div>
                                <div class="x-small text-muted mt-1">1 hour ago</div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="p-2 text-center border-top">
                    <a href="#" class="small text-decoration-none fw-bold">View all notifications</a>
                </div>
            </ul>
        </div>

        <!-- Profile Dropdown -->
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="profileDropdown"
                data-bs-toggle="dropdown" aria-expanded="false">
                <div class="avatar-circle bg-primary text-white d-flex align-items-center justify-content-center rounded-circle me-2 fw-bold"
                    style="width: 36px; height: 36px;">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div class="d-none d-lg-block text-start me-2">
                    <div class="small fw-bold text-dark lh-1">{{ auth()->user()->name }}</div>
                    <div class="x-small text-muted">{{ auth()->user()->email }}</div>
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-4" aria-labelledby="profileDropdown">
                <li>
                    <h6 class="dropdown-header text-uppercase small fw-bold">Account</h6>
                </li>
                <li><a class="dropdown-item" href="#"><i class="far fa-user me-2 opacity-50"></i> Profile</a></li>
                <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2 opacity-50"></i> Settings</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button class="dropdown-item text-danger fw-bold" type="submit"><i
                                class="fas fa-sign-out-alt me-2"></i> Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>