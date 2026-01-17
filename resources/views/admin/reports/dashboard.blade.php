@extends('admin.layouts.app')

@section('title', 'Reports & Analytics')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="fw-bold m-0">Reports & Analytics</h2>
                <div class="text-muted small">All dates displayed in IST</div>
            </div>
        </div>

        <x-admin.report-tabs />

        <x-admin.report-filters :action="route('admin.reports.dashboard')" :range="$range" />

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <x-admin.kpi-card
                    title="Total Gross Revenue"
                    value="INR {{ number_format($totalGross, 2) }}"
                    icon="fas fa-wallet"
                    variant="primary"
                    :href="route('admin.reports.revenue', request()->query())" />
            </div>
            <div class="col-md-3">
                <x-admin.kpi-card
                    title="Platform Commission"
                    value="INR {{ number_format($totalComm, 2) }}"
                    icon="fas fa-percent"
                    variant="success"
                    :href="route('admin.reports.revenue', request()->query())" />
            </div>
            <div class="col-md-3">
                <x-admin.kpi-card
                    title="Astrologer Earnings"
                    value="INR {{ number_format($totalEarn, 2) }}"
                    icon="fas fa-hand-holding-usd"
                    variant="info"
                    :href="route('admin.reports.revenue', request()->query())" />
            </div>
            <div class="col-md-3">
                <x-admin.kpi-card
                    title="Wallet Recharges (Success)"
                    value="INR {{ number_format($rechargeTotal, 2) }}"
                    icon="fas fa-bolt"
                    variant="warning"
                    subtitle="Rate: {{ number_format($rechargeRate, 1) }}%"
                    :href="route('admin.reports.recharges', array_merge(request()->query(), ['status' => 'success']))" />
            </div>
            <div class="col-md-3">
                <x-admin.kpi-card
                    title="Refunds"
                    value="INR {{ number_format($refundsTotal, 2) }}"
                    icon="fas fa-rotate-left"
                    variant="danger"
                    :href="route('admin.reports.refunds', request()->query())" />
            </div>
            <div class="col-md-3">
                <x-admin.kpi-card
                    title="Active Users"
                    value="{{ number_format($activeUsers) }}"
                    icon="fas fa-user-check"
                    variant="primary"
                    :href="route('admin.users.index', ['date_from' => $range['start_ist']->toDateString(), 'date_to' => $range['end_ist']->toDateString()])" />
            </div>
            <div class="col-md-3">
                <x-admin.kpi-card
                    title="New Users"
                    value="{{ number_format($newUsers) }}"
                    icon="fas fa-user-plus"
                    variant="success"
                    :href="route('admin.users.index', ['date_from' => $range['start_ist']->toDateString(), 'date_to' => $range['end_ist']->toDateString()])" />
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold m-0">Revenue Trend (Calls / Chats / AI)</h5>
                        <a href="{{ route('admin.reports.revenue', request()->query()) }}" class="btn btn-light btn-sm rounded-pill px-3">View Details</a>
                    </div>
                    <div class="card-body">
                        <canvas id="revenueTrend"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold m-0">Recharge Success vs Failed</h5>
                        <a href="{{ route('admin.reports.recharges', request()->query()) }}" class="btn btn-light btn-sm rounded-pill px-3">View Details</a>
                    </div>
                    <div class="card-body">
                        <canvas id="rechargeTrend"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold m-0">Top Astrologers by Earnings</h5>
                        <a href="{{ route('admin.reports.astrologers') }}" class="btn btn-light btn-sm rounded-pill px-3">View Leaderboard</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">Astrologer</th>
                                        <th>Email</th>
                                        <th class="text-end pe-4">Earnings</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topAstrologers as $astro)
                                        <tr>
                                            <td class="ps-4 fw-bold">{{ $astro->astrologer_name }}</td>
                                            <td class="text-muted">{{ $astro->astrologer_email }}</td>
                                            <td class="text-end pe-4 fw-bold">INR {{ number_format($astro->total_earnings, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-4">No earnings data for this range.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const trendLabels = {!! json_encode($revenueTrend->pluck('date_ist')->map(fn($d) => $d->format('d M'))) !!};
        const callSeries = {!! json_encode($revenueTrend->pluck('call_gross')) !!};
        const chatSeries = {!! json_encode($revenueTrend->pluck('chat_gross')) !!};
        const aiSeries = {!! json_encode($revenueTrend->pluck('ai_gross')) !!};

        const revenueCtx = document.getElementById('revenueTrend').getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: trendLabels,
                datasets: [
                    { label: 'Calls', data: callSeries, borderColor: '#4f46e5', backgroundColor: 'rgba(79, 70, 229, 0.15)', fill: true, tension: 0.3 },
                    { label: 'Chats', data: chatSeries, borderColor: '#10b981', backgroundColor: 'rgba(16, 185, 129, 0.15)', fill: true, tension: 0.3 },
                    { label: 'AI', data: aiSeries, borderColor: '#3b82f6', backgroundColor: 'rgba(59, 130, 246, 0.15)', fill: true, tension: 0.3 },
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } },
                scales: { y: { beginAtZero: true } }
            }
        });

        const rechargeLabels = {!! json_encode($rechargeTrend->pluck('date_ist')->map(fn($d) => $d->format('d M'))) !!};
        const rechargeSuccess = {!! json_encode($rechargeTrend->pluck('wallet_recharge_count_success')) !!};
        const rechargeFailed = {!! json_encode($rechargeTrend->pluck('wallet_recharge_count_failed')) !!};

        const rechargeCtx = document.getElementById('rechargeTrend').getContext('2d');
        new Chart(rechargeCtx, {
            type: 'bar',
            data: {
                labels: rechargeLabels,
                datasets: [
                    { label: 'Success', data: rechargeSuccess, backgroundColor: '#10b981' },
                    { label: 'Failed', data: rechargeFailed, backgroundColor: '#ef4444' }
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } },
                scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true } }
            }
        });
    </script>
@endsection



