<nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
    <div class="position-sticky pt-4">
        <div class="px-3 mb-4 text-white">
            <div class="fw-semibold">Astro Admin</div>
            <small class="text-muted">Role: {{ auth()->user()?->getRoleNames()->first() ?? 'User' }}</small>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    Dashboard
                </a>
            </li>
            @can('view_users')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                        Users
                    </a>
                </li>
            @endcan
            @can('manage_roles_permissions')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}" href="{{ route('admin.roles.index') }}">
                        Roles
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}" href="{{ route('admin.permissions.index') }}">
                        Permissions
                    </a>
                </li>
            @endcan
            @can('view_reports')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" href="#">
                        Reports
                    </a>
                </li>
            @endcan
            @can('manage_platform_settings')
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        Settings
                    </a>
                </li>
            @endcan
        </ul>
    </div>
</nav>
