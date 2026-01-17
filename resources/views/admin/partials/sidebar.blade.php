<nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
    <div class="position-sticky pt-4">
        <div class="px-3 mb-4 text-white">
            <div class="fw-semibold">Astro Admin</div>
            <small class="text-muted">Role: {{ auth()->user()?->getRoleNames()->first() ?? 'User' }}</small>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                    href="{{ route('admin.dashboard') }}">
                    Dashboard
                </a>
            </li>
            @can('view_users')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                        href="{{ route('admin.users.index') }}">
                        Users
                    </a>
                </li>
            @endcan
            @can('manage_roles_permissions')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}"
                        href="{{ route('admin.roles.index') }}">
                        Roles
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}"
                        href="{{ route('admin.permissions.index') }}">
                        Permissions
                    </a>
                </li>
            @endcan
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}"
                    href="{{ route('admin.reports.dashboard') }}">
                    Reports & Analytics
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.appointments.*') ? 'active' : '' }}"
                    href="{{ route('admin.appointments.index') }}">
                    Appointments
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.ai.settings') ? 'active' : '' }}"
                    href="{{ route('admin.ai.settings') }}">
                    AI Settings
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.pricing.index') ? 'active' : '' }}"
                    href="{{ route('admin.pricing.index') }}">
                    Pricing Settings
                </a>
            </li>
        </ul>
    </div>
</nav>
