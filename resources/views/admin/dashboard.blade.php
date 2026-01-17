@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row mb-4">
    <!-- Row 1 -->
    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('admin.users.index') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 py-2 stat-card border-primary">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Users</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totals['users']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300 opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('admin.astrologers.index') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 py-2 stat-card border-success">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Astrologers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totals['astrologers']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x text-gray-300 opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('admin.reports.revenue') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 py-2 stat-card border-info">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₹{{ number_format($totals['revenue'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-rupee-sign fa-2x text-gray-300 opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100 py-2 stat-card border-warning">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Today's Activity</div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800">
                            <i class="fas fa-phone-alt me-1"></i> {{ $totals['calls_count'] }} 
                            <span class="mx-2">|</span> 
                            <i class="fas fa-comments me-1"></i> {{ $totals['chats_count'] }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-2x text-gray-300 opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <!-- Row 2 -->
    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('admin.reports.recharges') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 py-2 stat-card border-left-secondary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Recharges</div>
                            <div class="h6 mb-0 fw-bold">
                                <span class="text-success">{{ $totals['recharges_success'] }}</span> / 
                                <span class="text-danger">{{ $totals['recharges_failed'] }}</span>
                            </div>
                        </div>
                        <i class="fas fa-bolt fa-lg text-secondary opacity-25"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('admin.astrologers.index', ['status' => 'pending']) }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 py-2 stat-card border-left-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Pending Verification</div>
                            <div class="h5 mb-0 fw-bold">{{ $totals['pending_verifications'] }}</div>
                        </div>
                        <i class="fas fa-user-clock fa-lg text-danger opacity-25"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100 py-2 stat-card border-left-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Commission Earned</div>
                        <div class="h5 mb-0 fw-bold">₹{{ number_format($totals['commission'], 2) }}</div>
                    </div>
                    <i class="fas fa-percent fa-lg text-success opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
         <a href="{{ route('admin.system.webhooks.index', ['status' => 'failed']) }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 py-2 stat-card border-left-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Failed Webhooks</div>
                            <div class="h5 mb-0 fw-bold">{{ $totals['failed_webhooks'] }}</div>
                        </div>
                        <i class="fas fa-exclamation-triangle fa-lg text-dark opacity-25"></i>
                    </div>
                </div>
            </div>
         </a>
    </div>
</div>

<div class="row">
    <!-- Chart -->
    <div class="col-xl-8 col-lg-7">
        <div class="card border-0 shadow-sm mb-4 rounded-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white rounded-top-4">
                <h6 class="m-0 font-weight-bold text-primary">Activity Overview</h6>
                 <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                        <div class="dropdown-header">Dropdown Header:</div>
                        <a class="dropdown-item" href="#">Action</a>
                        <a class="dropdown-item" href="#">Another action</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">Something else here</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-area" style="height: 320px;">
                    <canvas id="activityChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
         <div class="card border-0 shadow-sm mb-4 rounded-4">
            <div class="card-header py-3 bg-white rounded-top-4">
                <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary rounded-pill"><i class="fas fa-user-plus me-1"></i> Add User</a>
                    <a href="{{ route('admin.astrologers.index') }}" class="btn btn-outline-success rounded-pill"><i class="fas fa-star me-1"></i> Verify Astro</a>
                    <a href="{{ route('admin.wallets.index') }}" class="btn btn-outline-info rounded-pill"><i class="fas fa-wallet me-1"></i> Credit Wallet</a>
                    <a href="{{ route('admin.reports.revenue') }}" class="btn btn-outline-secondary rounded-pill"><i class="fas fa-download me-1"></i> Export Report</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Widgets Column -->
    <div class="col-xl-4 col-lg-5">
        <!-- Pending Verifications -->
        <div class="card border-0 shadow-sm mb-4 rounded-4">
            <div class="card-header py-3 bg-white rounded-top-4 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-danger">Pending Verifications</h6>
                <a href="{{ route('admin.astrologers.index', ['status' => 'pending']) }}" class="small text-decoration-none">View All</a>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($widgets['pending_verifications'] as $pv)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-4 py-3">
                            <div>
                                <div class="font-weight-bold text-dark">{{ $pv->name }}</div>
                                <div class="small text-muted">{{ $pv->email }}</div>
                            </div>
                            <a href="{{ route('admin.astrologers.show', $pv->id) }}" class="btn btn-sm btn-light rounded-pill">Review</a>
                        </li>
                    @empty
                        <li class="list-group-item text-center py-4 text-muted small">No pending verifications.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Latest Payments -->
         <div class="card border-0 shadow-sm mb-4 rounded-4">
            <div class="card-header py-3 bg-white rounded-top-4">
                <h6 class="m-0 font-weight-bold text-success">Latest Payments</h6>
            </div>
             <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                     @forelse($widgets['latest_payments'] as $lp)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-4 py-3">
                            <div>
                                <div class="font-weight-bold text-dark">{{ $lp->name }}</div>
                                <div class="small text-muted">{{ \Carbon\Carbon::parse($lp->created_at)->diffForHumans() }}</div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-success">+₹{{ $lp->amount }}</div>
                                <span class="badge bg-success-subtle text-success rounded-pill" style="font-size: 0.65rem;">{{ strtoupper($lp->status) }}</span>
                            </div>
                        </li>
                     @empty
                        <li class="list-group-item text-center py-4 text-muted small">No recent payments.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Alert Widget -->
        @if(count($widgets['system_alerts']) > 0)
        <div class="card border-0 shadow-sm mb-4 rounded-4">
             <div class="card-header py-3 bg-white rounded-top-4">
                <h6 class="m-0 font-weight-bold text-warning">System Alerts</h6>
            </div>
            <div class="card-body p-0">
                 <ul class="list-group list-group-flush">
                    @foreach($widgets['system_alerts'] as $alert)
                        <li class="list-group-item px-4 py-3 border-left-{{ $alert['type'] }}">
                             <div class="d-flex align-items-start">
                                <i class="fas fa-exclamation-circle text-{{ $alert['type'] }} mt-1 me-2"></i>
                                <div>
                                    <div class="small fw-bold text-dark">{{ $alert['message'] }}</div>
                                    <a href="{{ $alert['link'] }}" class="x-small text-decoration-none">Resolve Now &rarr;</a>
                                </div>
                             </div>
                        </li>
                    @endforeach
                 </ul>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    const ctx = document.getElementById('activityChart').getContext('2d');
    const activityChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chart['labels']) !!},
            datasets: [
                {
                    label: 'Calculated Calls',
                    data: {!! json_encode($chart['series']['calls']) !!},
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Chats',
                    data: {!! json_encode($chart['series']['chats']) !!},
                    borderColor: '#1cc88a',
                    backgroundColor: 'rgba(28, 200, 138, 0.05)',
                    tension: 0.3,
                    fill: true
                },
                 {
                    label: 'Recharges',
                    data: {!! json_encode($chart['series']['recharges']) !!},
                    borderColor: '#f6c23e',
                    backgroundColor: 'rgba(246, 194, 62, 0.05)',
                     tension: 0.3,
                    fill: true
                }
            ]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            },
            scales: {
                y: { beginAtZero: true, grid: { borderDash: [2] } },
                x: { grid: { display: false } }
            },
             interaction: {
                intersect: false,
                mode: 'index',
            },
        }
    });
</script>
@endsection