@extends('admin.layouts.app')

@section('title', 'Referrals')
@section('page_title', 'Referral Management')

@section('content')
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Total Referrals</h6>
                    <h3 class="mb-0">{{ $stats['total'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Qualified</h6>
                    <h3 class="mb-0 text-success">{{ $stats['qualified'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Rewards Paid</h6>
                    <h3 class="mb-0">₹{{ number_format($stats['rewards_paid'], 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Conversion Rate</h6>
                    <h3 class="mb-0">{{ $stats['conversion_rate'] }}%</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Top Referrers</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>User</th>
                                    <th>Code</th>
                                    <th>Total Referrals</th>
                                    <th>Qualified</th>
                                    <th>Earned</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topReferrers as $index => $referrer)
                                    <tr>
                                        <td>
                                            @if($index == 0) 🥇
                                            @elseif($index == 1) 🥈
                                            @elseif($index == 2) 🥉
                                            @else {{ $index + 1 }}
                                            @endif
                                        </td>
                                        <td>
                                            <div>{{ $referrer->user->name }}</div>
                                            <small class="text-muted">{{ $referrer->user->phone }}</small>
                                        </td>
                                        <td><code>{{ $referrer->code }}</code></td>
                                        <td>{{ $referrer->referrals_count }}</td>
                                        <td>{{ $referrer->qualified_count }}</td>
                                        <td>₹{{ number_format($referrer->total_earned, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Recent Referrals</h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @foreach($recentReferrals as $referral)
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold">{{ $referral->invitee->name }}</div>
                                        <small class="text-muted">via {{ $referral->inviter->name }}</small>
                                    </div>
                                    <span class="badge bg-{{ $referral->status == 'qualified' ? 'success' : 'warning' }}">
                                        {{ ucfirst($referral->status) }}
                                    </span>
                                </div>
                                <small class="text-muted">{{ $referral->created_at->diffForHumans() }}</small>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection