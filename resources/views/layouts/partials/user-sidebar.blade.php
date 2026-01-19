<!-- Sidebar Container (Shared for Mobile & Desktop) -->
<div class="offcanvas-lg offcanvas-start bg-white border-end" tabindex="-1" id="sidebarMenu"
    aria-labelledby="sidebarMenuLabel" style="width: 280px;">

    <!-- Header -->
    <div class="offcanvas-header border-bottom h-16 d-flex align-items-center px-4">
        <h5 class="offcanvas-title sidebar-brand fs-4 m-0" id="sidebarMenuLabel">AI Astro</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebarMenu"
            aria-label="Close"></button>
    </div>

    <!-- Body (Nav) -->
    <div class="offcanvas-body p-0 d-flex flex-column h-100 overflow-y-auto">

        <!-- Desktop Logo (Only visible when not offcanvas in LG screens if we want, or just hide header on desktop) -->
        <div class="d-none d-lg-flex align-items-center h-16 px-4 border-bottom bg-primary text-white">
            <span class="fs-4 fw-bold">AI Astro</span>
        </div>

        <nav class="list-group list-group-flush flex-grow-1 p-3 gap-1 border-0">
            <x-user.nav-link :href="route('user.dashboard')" :active="request()->routeIs('user.dashboard')" icon="home">
                Dashboard
            </x-user.nav-link>

            <x-user.nav-link :href="route('user.astrologers.index')" :active="request()->routeIs('user.astrologers.*')"
                icon="users">
                Browse Astrologers
            </x-user.nav-link>

            <x-user.nav-link :href="route('user.calls.index')" :active="request()->routeIs('user.calls.*')"
                icon="phone">
                Call History
            </x-user.nav-link>

            <x-user.nav-link :href="route('user.chat.index')" :active="request()->routeIs('user.chat.*')" icon="chat">
                Chats
            </x-user.nav-link>

            <x-user.nav-link :href="route('user.appointments.index')"
                :active="request()->routeIs('user.appointments.*')" icon="calendar">
                Appointments
            </x-user.nav-link>

            <x-user.nav-link :href="route('user.ai.index')" :active="request()->routeIs('user.ai.*')" icon="sparkles">
                AI Assistant
            </x-user.nav-link>

            <x-user.nav-link :href="route('user.horoscope.index')" :active="request()->routeIs('user.horoscope.*')"
                icon="document-text">
                Horoscope & Reports
            </x-user.nav-link>

            <hr class="my-2">

            <x-user.nav-link :href="route('user.wallet.index')" :active="request()->routeIs('user.wallet.*')"
                icon="credit-card">
                Wallet (₹{{ auth()->user()->wallet_balance ?? 0 }})
            </x-user.nav-link>

            <x-user.nav-link :href="route('user.profile.index')" :active="request()->routeIs('user.profile.*')"
                icon="user">
                Profile
            </x-user.nav-link>

            <x-user.nav-link :href="route('user.support.index')" :active="request()->routeIs('user.support.*')"
                icon="support">
                Support
            </x-user.nav-link>

            <x-user.nav-link :href="route('user.settings.index')" :active="request()->routeIs('user.settings.*')"
                icon="cog">
                Settings
            </x-user.nav-link>

            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <button type="submit"
                    class="list-group-item list-group-item-action border-0 d-flex align-items-center gap-3 py-3 text-danger">
                    <i class="bi bi-box-arrow-right fs-5"></i>
                    <span class="fw-medium">Sign Out</span>
                </button>
            </form>
        </nav>
    </div>
</div>