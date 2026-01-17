@php
    $tabs = [
        ['label' => 'Overview', 'route' => 'admin.reports.dashboard'],
        ['label' => 'Revenue', 'route' => 'admin.reports.revenue'],
        ['label' => 'Commission & Earnings', 'route' => null, 'disabled' => true],
        ['label' => 'Wallet Recharges', 'route' => 'admin.reports.recharges'],
        ['label' => 'Calls Analytics', 'route' => 'admin.reports.calls'],
        ['label' => 'Chats Analytics', 'route' => 'admin.reports.chats'],
        ['label' => 'AI Analytics', 'route' => 'admin.reports.ai'],
        ['label' => 'Astrologer Performance', 'route' => 'admin.reports.astrologers'],
        ['label' => 'User Funnel', 'route' => null, 'disabled' => true],
        ['label' => 'Refunds/Disputes', 'route' => 'admin.reports.refunds'],
    ];
@endphp

<div class="mb-4">
    <ul class="nav nav-pills gap-2 flex-wrap">
        @foreach($tabs as $tab)
            @php
                $isActive = $tab['route'] ? request()->routeIs($tab['route']) : false;
                $isDisabled = $tab['disabled'] ?? false;
            @endphp
            <li class="nav-item">
                @if($isDisabled)
                    <span class="nav-link disabled text-muted" aria-disabled="true">{{ $tab['label'] }}</span>
                @else
                    <a class="nav-link {{ $isActive ? 'active' : 'text-muted' }}"
                        href="{{ route($tab['route'], request()->except('page')) }}">
                        {{ $tab['label'] }}
                    </a>
                @endif
            </li>
        @endforeach
    </ul>
</div>
