<div class="card shadow-sm border-0 mb-3">
    <div class="card-body p-2">
        <div class="nav flex-column nav-pills">
            <a href="{{ route('astrologer.profile') }}"
                class="nav-link {{ request()->routeIs('astrologer.profile') ? 'active' : '' }}">
                <i class="fas fa-user-edit me-2"></i> Profile
            </a>
            <a href="{{ route('astrologer.services') }}"
                class="nav-link {{ request()->routeIs('astrologer.services') ? 'active' : '' }}">
                <i class="fas fa-tags me-2"></i> Services & Pricing
            </a>
            <a href="{{ route('astrologer.availability') }}"
                class="nav-link {{ request()->routeIs('astrologer.availability') ? 'active' : '' }}">
                <i class="fas fa-clock me-2"></i> Availability
            </a>
            <a href="{{ route('astrologer.appointments.index') }}"
                class="nav-link {{ request()->routeIs('astrologer.appointments.*') ? 'active' : '' }}">
                <i class="fas fa-calendar-check me-2"></i> Appointments
            </a>
            <a href="{{ route('astrologer.calls') }}"
                class="nav-link {{ request()->routeIs('astrologer.calls') ? 'active' : '' }}">
                <i class="fas fa-phone me-2"></i> Call History
            </a>
            <a href="{{ route('astrologer.appointments.index') }}"
                class="nav-link {{ request()->routeIs('astrologer.appointments.*') ? 'active' : '' }}">
                <i class="fas fa-calendar-check me-2"></i> Appointments
            </a>
            <a href="{{ route('astrologer.chats') }}"
                class="nav-link {{ request()->routeIs('astrologer.chats') ? 'active' : '' }}">
                <i class="fas fa-comments me-2"></i> Active Chats
            </a>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 bg-light">
    <div class="card-body">
        <h6 class="fw-bold mb-2">Status</h6>
        @if($profile->is_verified)
            <span class="badge bg-success w-100 py-2"><i class="fas fa-check-circle"></i> Verified</span>
        @else
            <span class="badge bg-warning text-dark w-100 py-2"><i class="fas fa-clock"></i> Verification Pending</span>
        @endif
    </div>
</div>
