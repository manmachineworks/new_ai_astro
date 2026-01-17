<nav class="navbar fixed-bottom navbar-dark bg-dark d-lg-none border-top border-secondary"
    style="padding-bottom: env(safe-area-inset-bottom);">
    <div class="container-fluid d-flex justify-content-around">
        <a href="{{ url('/') }}"
            class="text-decoration-none text-center {{ Request::is('/') ? 'text-warning' : 'text-muted' }}">
            <i class="fas fa-home fs-5"></i>
            <div style="font-size: 0.7rem;">Home</div>
        </a>
        <a href="#"
            class="text-decoration-none text-center {{ Request::is('astrologers*') ? 'text-warning' : 'text-muted' }}">
            <i class="fas fa-user-astronaut fs-5"></i>
            <div style="font-size: 0.7rem;">Astros</div>
        </a>
        <a href="#" class="text-decoration-none text-center {{ Request::is('chat*') ? 'text-warning' : 'text-muted' }}">
            <i class="fas fa-comments fs-5"></i>
            <div style="font-size: 0.7rem;">Chat</div>
        </a>
        <a href="#"
            class="text-decoration-none text-center {{ Request::is('wallet*') ? 'text-warning' : 'text-muted' }}">
            <i class="fas fa-wallet fs-5"></i>
            <div style="font-size: 0.7rem;">Wallet</div>
        </a>
        <a href="{{ auth()->check() ? route('dashboard') : route('login') }}"
            class="text-decoration-none text-center {{ Request::is('dashboard*') || Request::is('login') ? 'text-warning' : 'text-muted' }}">
            <i class="fas fa-user fs-5"></i>
            <div style="font-size: 0.7rem;">{{ auth()->check() ? 'Profile' : 'Login' }}</div>
        </a>
    </div>
</nav>

<style>
    /* Add padding to body so content isn't hidden behind bottom nav */
    @media (max-width: 991.98px) {
        body {
            padding-bottom: 70px;
        }
    }
</style>