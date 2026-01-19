<nav class="col-md-3 col-lg-2 d-md-block sidebar collapse bg-dark border-end border-secondary" id="sidebarMenu">
    <div class="position-sticky pt-3 pb-3">
        <!-- Brand -->
        <div class="px-3 mb-4 d-flex align-items-center text-white">
            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2"
                style="width: 32px; height: 32px;">
                <i class="fas fa-meteor text-white"></i>
            </div>
            <div>
                <div class="fw-bold tracking-tight">Astro Admin</div>
                <small class="text-white-50"
                    style="font-size: 0.75rem;">{{ auth()->user()->getRoleNames()->first() ?? 'Staff' }}</small>
            </div>
        </div>

        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link text-white-50 {{ request()->routeIs('admin.dashboard') ? 'active text-white bg-secondary bg-opacity-25 border-end border-3 border-primary' : '' }}"
                    href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-home me-2"></i> Dashboard
                </a>
            </li>
        </ul>

        @php
            $canAccessControl = auth()->user()->can('manage_roles');
            $canManageUsers = auth()->user()->can('view_users');
            $canManageAstrologers = auth()->user()->can('view_astrologers');
            $canOperations = auth()->user()->can('view_calls') || auth()->user()->can('view_chats') || auth()->user()->can('view_ai_chats');
            $canModeration = auth()->user()->can('manage_chats') || auth()->user()->can('manage_reviews');
            $canSystem = auth()->user()->can('view_webhooks') || auth()->user()->can('view_audit_logs') || auth()->user()->can('manage_ai_settings') || auth()->user()->can('manage_commissions');
        @endphp

        @if($canAccessControl)
            <h6
                class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-2 text-uppercase text-white-50 fs-7 fw-bold">
                <span>Access Control</span>
            </h6>
            <ul class="nav flex-column mb-2">
                <li class="nav-item">
                    <a class="nav-link text-white-50 {{ request()->routeIs('admin.admin-users.*') ? 'active text-white bg-secondary bg-opacity-25 border-end border-3 border-primary' : '' }}"
                        href="{{ route('admin.admin-users.index') }}">
                        <i class="fas fa-user-cog me-2"></i> Admin Users
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white-50 {{ request()->routeIs('admin.roles.*') ? 'active text-white bg-secondary bg-opacity-25 border-end border-3 border-primary' : '' }}"
                        href="{{ route('admin.roles.index') }}">
                        <i class="fas fa-user-shield me-2"></i> Roles & Perms
                    </a>
                </li>
            </ul>
        @endif

        @if($canManageUsers || $canManageAstrologers)
            <h6
                class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-2 text-uppercase text-white-50 fs-7 fw-bold">
                <span>Management</span>
            </h6>
            <ul class="nav flex-column mb-2">
                @can('view_users')
                    <li class="nav-item">
                        <a class="nav-link text-white-50 {{ request()->routeIs('admin.users.*') ? 'active text-white bg-secondary bg-opacity-25 border-end border-3 border-primary' : '' }}"
                            href="{{ route('admin.users.index') }}">
                            <i class="fas fa-users me-2"></i> Users
                        </a>
                    </li>
                @endcan
                @can('view_astrologers')
                    <li class="nav-item">
                        <a class="nav-link text-white-50 {{ request()->routeIs('admin.astrologers.*') ? 'active text-white bg-secondary bg-opacity-25 border-end border-3 border-primary' : '' }}"
                            href="{{ route('admin.astrologers.index') }}">
                            <i class="fas fa-star me-2"></i> Astrologers
                        </a>
                    </li>
                @endcan
            </ul>
        @endif

        @if($canOperations || auth()->user()->can('view_users'))
            <h6
                class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-2 text-uppercase text-white-50 fs-7 fw-bold">
                <span>Operations</span>
            </h6>
            <ul class="nav flex-column mb-2">
                @can('view_calls')
                    <li class="nav-item">
                        <a class="nav-link text-white-50 {{ request()->routeIs('admin.calls.*') ? 'active text-white bg-secondary bg-opacity-25 border-end border-3 border-primary' : '' }}"
                            href="{{ route('admin.calls.index') }}">
                            <i class="fas fa-phone me-2"></i> Calls
                        </a>
                    </li>
                @endcan
                @can('view_chats')
                    <li class="nav-item">
                        <a class="nav-link text-white-50 {{ request()->routeIs('admin.chats.*') ? 'active text-white bg-secondary bg-opacity-25 border-end border-3 border-primary' : '' }}"
                            href="{{ route('admin.chats.index') }}">
                            <i class="fas fa-comments me-2"></i> Chats
                        </a>
                    </li>
                @endcan
                @can('view_ai_chats')
                    <li class="nav-item">
                        <a class="nav-link text-white-50 {{ request()->routeIs('admin.ai_chats.*') ? 'active text-white bg-secondary bg-opacity-25 border-end border-3 border-primary' : '' }}"
                            href="{{ route('admin.ai_chats.index') }}">
                            <i class="fas fa-robot me-2"></i> AI Chats
                        </a>
                    </li>
                @endcan
                @canany(['view_users', 'view_astrologers', 'view_calls'])
                    <li class="nav-item">
                        <a class="nav-link text-white-50 {{ request()->routeIs('admin.appointments.*') ? 'active text-white bg-secondary bg-opacity-25 border-end border-3 border-primary' : '' }}"
                            href="{{ route('admin.appointments.index') }}">
                            <i class="fas fa-calendar-check me-2"></i> Appointments
                        </a>
                    </li>
                @endcanany
            </ul>
        @endif

        @if($canModeration)
            <h6
                class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-2 text-uppercase text-white-50 fs-7 fw-bold">
                <span>Moderation</span>
            </h6>
            <ul class="nav flex-column mb-2">
                @can('manage_chats')
                    <li class="nav-item">
                        <a class="nav-link text-white-50 {{ request()->routeIs('admin.moderation.chats.*') ? 'active text-white bg-secondary bg-opacity-25 border-end border-3 border-primary' : '' }}"
                            href="{{ route('admin.moderation.chats.index') }}">
                            <i class="fas fa-comment-dots me-2"></i> Chat Moderation
                        </a>
                    </li>
                @endcan
                @can('manage_reviews')
                    <li class="nav-item">
                        <a class="nav-link text-white-50 {{ request()->routeIs('admin.reviews.*') ? 'active text-white bg-secondary bg-opacity-25 border-end border-3 border-primary' : '' }}"
                            href="{{ route('admin.reviews.index') }}">
                            <i class="fas fa-star-half-alt me-2"></i> Reviews
                        </a>
                    </li>
                @endcan
                @can('manage_reviews')
                    <li class="nav-item">
                        <a class="nav-link text-white-50 {{ request()->routeIs('admin.disputes.*') ? 'active text-white bg-secondary bg-opacity-25 border-end border-3 border-primary' : '' }}"
                            href="{{ route('admin.disputes.index') }}">
                            <i class="fas fa-life-ring me-2"></i> Complaints
                        </a>
                    </li>
                @endcan
                @can('manage_reviews')
                    <li class="nav-item">
                        <a class="nav-link text-white-50 {{ request()->routeIs('admin.support.*') ? 'active text-white bg-secondary bg-opacity-25 border-end border-3 border-primary' : '' }}"
                            href="{{ route('admin.support.index') }}">
                            <i class="fas fa-headset me-2"></i> Support Tickets
                        </a>
                    </li>
                @endcan
                @can('manage_chats')
                    <li class="nav-item">
                        <a class="nav-link text-white-50 {{ request()->routeIs('admin.moderation.banned_words.*') ? 'active text-white bg-secondary bg-opacity-25 border-end border-3 border-primary' : '' }}"
                            href="{{ route('admin.moderation.banned_words.index') }}">
                            <i class="fas fa-ban me-2"></i> Banned Words
                        </a>
                    </li>
                @endcan
            </ul>
        @endif

        <!-- Finance Ops Group -->
        @can('view_finance')
            <h6
                class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-2 text-uppercase text-white-50 fs-7 fw-bold">
                <span>Finance Ops</span>
            </h6>
            <ul class="nav flex-column mb-2">
                <li class="nav-item">
                    <a class="nav-link text-white-50 {{ request()->routeIs('admin.finance.payments.*') ? 'active text-white bg-secondary bg-opacity-25 border-end border-3 border-primary' : '' }}"
                        href="{{ route('admin.finance.payments.index') }}">
                        <i class="fas fa-credit-card me-2"></i> Payments
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white-50 {{ request()->routeIs('admin.finance.wallets.*') ? 'active text-white bg-secondary bg-opacity-25 border-end border-3 border-primary' : '' }}"
                        href="{{ route('admin.finance.wallets.index') }}">
                        <i class="fas fa-wallet me-2"></i> Wallet Ledger
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white-50 {{ request()->routeIs('admin.finance.earnings.*') ? 'active text-white bg-secondary bg-opacity-25 border-end border-3 border-primary' : '' }}"
                        href="{{ route('admin.finance.earnings.index') }}">
                        <i class="fas fa-hand-holding-usd me-2"></i> Earnings & Payouts
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white-50 {{ request()->routeIs('admin.finance.refunds.*') ? 'active text-white bg-secondary bg-opacity-25 border-end border-3 border-primary' : '' }}"
                        href="{{ route('admin.finance.refunds.index') }}">
                        <i class="fas fa-rotate-left me-2"></i> Refunds
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white-50 {{ request()->routeIs('admin.finance.commissions.*') ? 'active text-white bg-secondary bg-opacity-25 border-end border-3 border-primary' : '' }}"
                        href="{{ route('admin.finance.commissions.index') }}">
                        <i class="fas fa-tags me-2"></i> Commission Settings
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white-50 {{ request()->routeIs('admin.finance.exports.*') ? 'active text-white bg-secondary bg-opacity-25 border-end border-3 border-primary' : '' }}"
                        href="{{ route('admin.finance.exports.index') }}">
                        <i class="fas fa-file-csv me-2"></i> Finance Exports
                    </a>
                </li>
            </ul>
        @endcan

        <!-- Reports Group -->
        @can('view_finance')
            <h6
                class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-2 text-uppercase text-white-50 fs-7 fw-bold">
                <span>Reporting</span>
            </h6>
            <ul class="nav flex-column mb-2">
                <li class="nav-item">
                    <a class="nav-link text-white-50 {{ request()->routeIs('admin.reports.dashboard') ? 'active text-white bg-secondary bg-opacity-25 border-end border-3 border-primary' : '' }}"
                        href="{{ route('admin.reports.dashboard') }}">
                        <i class="fas fa-chart-pie me-2"></i> Overview
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white-50 {{ request()->routeIs('admin.reports.revenue') ? 'active text-white bg-secondary bg-opacity-25 border-end border-3 border-primary' : '' }}"
                        href="{{ route('admin.reports.revenue') }}">
                        <i class="fas fa-chart-line me-2"></i> Revenue
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white-50 {{ request()->routeIs('admin.reports.recharges') ? 'active text-white bg-secondary bg-opacity-25 border-end border-3 border-primary' : '' }}"
                        href="{{ route('admin.reports.recharges') }}">
                        <i class="fas fa-bolt me-2"></i> Recharges
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white-50 {{ request()->routeIs('admin.reports.astrologers') ? 'active text-white bg-secondary bg-opacity-25 border-end border-3 border-primary' : '' }}"
                        href="{{ route('admin.reports.astrologers') }}">
                        <i class="fas fa-trophy me-2"></i> Top Astrologers
                    </a>
                </li>
            </ul>
        @endcan

        <!-- System Group -->
        @if($canSystem)
            <h6
                class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-2 text-uppercase text-white-50 fs-7 fw-bold">
                <span>System</span>
            </h6>
            <ul class="nav flex-column mb-2">
                @can('view_webhooks')
                    <li class="nav-item">
                        <a class="nav-link text-white-50 {{ request()->routeIs('admin.system.webhooks.*') ? 'active text-white bg-secondary bg-opacity-25 border-end border-3 border-primary' : '' }}"
                            href="{{ route('admin.system.webhooks.index') }}">
                            <i class="fas fa-satellite-dish me-2"></i> Webhooks
                        </a>
                    </li>
                @endcan
                @can('view_audit_logs')
                    <li class="nav-item">
                        <a class="nav-link text-white-50 {{ request()->routeIs('admin.system.audit_logs.*') ? 'active text-white bg-secondary bg-opacity-25 border-end border-3 border-primary' : '' }}"
                            href="{{ route('admin.system.audit_logs.index') }}">
                            <i class="fas fa-history me-2"></i> Audit Logs
                        </a>
                    </li>
                @endcan
                @can('manage_ai_settings')
                    <li class="nav-item">
                        <a class="nav-link text-white-50 {{ request()->routeIs('admin.ai.settings') ? 'active text-white bg-secondary bg-opacity-25 border-end border-3 border-primary' : '' }}"
                            href="{{ route('admin.ai.settings') }}">
                            <i class="fas fa-robot me-2"></i> AI Settings
                        </a>
                    </li>
                @endcan
                @can('manage_commissions')
                    <li class="nav-item">
                        <a class="nav-link text-white-50 {{ request()->routeIs('admin.pricing.*') ? 'active text-white bg-secondary bg-opacity-25 border-end border-3 border-primary' : '' }}"
                            href="{{ route('admin.pricing.index') }}">
                            <i class="fas fa-tags me-2"></i> Pricing Rules
                        </a>
                    </li>
                @endcan
            </ul>
        @endif
    </div>
</nav>
