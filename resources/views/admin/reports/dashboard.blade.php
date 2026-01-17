@extends('admin.layouts.app')

@section('title', 'Marketplace Analytics')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold m-0"><i class="fas fa-chart-line me-2 text-primary"></i>Analytics Overview</h2>

            <form action="{{ route('admin.reports.dashboard') }}" method="GET"
                class="d-flex gap-2 align-items-center bg-white p-2 rounded-4 shadow-sm">
                <select name="preset" class="form-select form-select-sm border-0 bg-light rounded-pill px-3"
                    onchange="this.form.submit()">
                    <option value="today" {{ $filters['preset'] == 'today' ? 'selected' : '' }}>Today</option>
                    <option value="yesterday" {{ $filters['preset'] == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                    <option value="last_7_days" {{ $filters['preset'] == 'last_7_days' ? 'selected' : '' }}>Last 7 Days
                    </option>
                    <option value="last_30_days" {{ $filters['preset'] == 'last_30_days' ? 'selected' : '' }}>Last 30 Days
                    </option>
                    <option value="this_month" {{ $filters['preset'] == 'this_month' ? 'selected' : '' }}>This Month</option>
                </select>
                <input type="date" name="start_date"
                    class="form-control form-control-sm border-0 bg-light rounded-pill px-3"
                    value="{{ $filters['start']->toDateString() }}">
                <span class="text-muted small">to</span>
                <input type="date" name="end_date" class="form-control form-control-sm border-0 bg-light rounded-pill px-3"
                    value="{{ $filters['end']->toDateString() }}">
                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3">Filter</button>
            </form>
        </div>

        <!-- KPI Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between mb-3">
                            <div class="bg-primary-subtle p-3 rounded-4"><i class="fas fa-wallet text-primary"></i></div>
                            <span class="text-success small fw-bold"><i class="fas fa-arrow-up"></i> Gross</span>
                        </div>
                        <h3 class="fw-bold mb-1">₹{{ number_format($totalGross, 2) }}</h3>
                        <p class="text-muted small mb-0">Total Marketplace Revenue</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between mb-3">
                            <div class="bg-success-subtle p-3 rounded-4"><i class="fas fa-percent text-success"></i></div>
                            <span class="text-primary small fw-bold">Platform</span>
                        </div>
                        <h3 class="fw-bold mb-1">₹{{ number_format($totalComm, 2) }}</h3>
                        <p class="text-muted small mb-0">Total Commission Earned</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between mb-3">
                            <div class="bg-info-subtle p-3 rounded-4"><i class="fas fa-hand-holding-usd text-info"></i>
                            </div>
                            <span class="text-info small fw-bold">Success Rate:
                                {{ number_format($rechargeRate, 1) }}%</span>
                        </div>
                        <h3 class="fw-bold mb-1">₹{{ number_format($rechargeTotal, 2) }}</h3>
                        <p class="text-muted small mb-0">Wallet Recharge Volume</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between mb-3">
                            <div class="bg-warning-subtle p-3 rounded-4"><i class="fas fa-users text-warning"></i></div>
                            <span class="text-warning small fw-bold">New: {{ $metrics->total_new_users ?? 0 }}</span>
                        </div>
                        <h3 class="fw-bold mb-1">{{ number_format($metrics->peak_active_users ?? 0) }}</h3>
                        <p class="text-muted small mb-0">Peak Active Users</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="row g-4 mb-4">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 py-4 px-4">
                        <h5 class="fw-bold m-0">Revenue Trends</h5>
                    </div>
                    <div class="card-body p-4">
                        <canvas id="revenueChart" style="height: 350px;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 py-4 px-4">
                        <h5 class="fw-bold m-0">Service Breakdown</h5>
                    </div>
                    <div class="card-body p-4 d-flex align-items-center">
                        <canvas id="breakdownChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Summaries -->
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold m-0">Revenue by Category</h5>
                        <a href="{{ route('admin.reports.revenue') }}" class="btn btn-light btn-sm rounded-pill px-3">View
                            Details</a>
                    </div>
                    <div class="card-body p-0">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Category</th>
                                    <th>Gross</th>
                                    <th>Commission</th>
                                    <th class="pe-4 text-end">Net (Astro)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="ps-4 fw-bold"><i class="fas fa-phone me-2 text-primary"></i>Calls</td>
                                    <td>₹{{ number_format($metrics->call_gross ?? 0, 2) }}</td>
                                    <td>₹{{ number_format($metrics->call_commission ?? 0, 2) }}</td>
                                    <td class="pe-4 text-end">₹{{ number_format($metrics->call_earnings ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="ps-4 fw-bold"><i class="fas fa-comments me-2 text-success"></i>Human Chat
                                    </td>
                                    <td>₹{{ number_format($metrics->chat_gross ?? 0, 2) }}</td>
                                    <td>₹{{ number_format($metrics->chat_commission ?? 0, 2) }}</td>
                                    <td class="pe-4 text-end">₹{{ number_format($metrics->chat_earnings ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="ps-4 fw-bold"><i class="fas fa-robot me-2 text-info"></i>AI Chat</td>
                                    <td>₹{{ number_format($metrics->ai_gross ?? 0, 2) }}</td>
                                    <td>₹{{ number_format($metrics->ai_commission ?? 0, 2) }}</td>
                                    <td class="pe-4 text-end">₹{{ number_format($metrics->ai_earnings ?? 0, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold m-0">Platform Health</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small fw-bold">Recharge Success Rate</span>
                                <span class="small fw-bold text-success">{{ number_format($rechargeRate, 1) }}%</span>
                            </div>
                            <div class="progress rounded-pill" style="height: 10px;">
                                <div class="progress-bar bg-success" style="width: {{ $rechargeRate }}%"></div>
                            </div>
                        </div>
                        <div class="row text-center g-3">
                            <div class="col-6">
                                <div class="bg-light p-3 rounded-4">
                                    <div class="text-muted small mb-1">Successful Recharges</div>
                                    <h4 class="fw-bold m-0 text-success">{{ $metrics->recharge_count_ok ?? 0 }}</h4>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-light p-3 rounded-4">
                                    <div class="text-muted small mb-1">Failed Recharges</div>
                                    <h4 class="fw-bold m-0 text-danger">{{ $metrics->recharge_count_fail ?? 0 }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const chartLabels = {!! json_encode($chartData->pluck('date_ist')->map(fn($d) => $d->format('d M'))) !!};
        const callData = {!! json_encode($chartData->pluck('call_gross')) !!};
        const chatData = {!! json_encode($chartData->pluck('chat_gross')) !!};
        const aiData = {!! json_encode($chartData->pluck('ai_gross')) !!};

        const ctx = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [
                    {
                        label: 'Voice Calls',
                        data: callData,
                        borderColor: '#4e73df',
                        backgroundColor: 'rgba(78, 115, 223, 0.05)',
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: 'Human Chat',
                        data: chatData,
                        borderColor: '#1cc88a',
                        backgroundColor: 'rgba(28, 200, 138, 0.05)',
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: 'AI Chat',
                        data: aiData,
                        borderColor: '#36b9cc',
                        backgroundColor: 'rgba(54, 185, 204, 0.05)',
                        fill: true,
                        tension: 0.3
                    }
                ]
            },
            options: {
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } },
                scales: {
                    y: { beginAtZero: true, grid: { drawBorder: false } },
                    x: { grid: { display: false } }
                }
            }
        });

        const breakdownCtx = document.getElementById('breakdownChart').getContext('2d');
        new Chart(breakdownCtx, {
            type: 'doughnut',
            data: {
                labels: ['Calls', 'Human Chat', 'AI Chat'],
                datasets: [{
                    data: [
                        {{ $metrics->call_gross ?? 0 }},
                        {{ $metrics->chat_gross ?? 0 }},
                        {{ $metrics->ai_gross ?? 0 }}
                    ],
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
                    hoverOffset: 10
                }]
            },
            options: {
                plugins: { legend: { position: 'bottom' } }
            }
        });

    </script>
@endsection