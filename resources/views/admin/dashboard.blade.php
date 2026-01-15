@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')
@section('page_title', 'Dashboard')

@section('content')
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card stat-card shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Total Users</div>
                    <div class="h4 mb-0">{{ $totals['users'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Total Astrologers</div>
                    <div class="h4 mb-0">{{ $totals['astrologers'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Total Revenue</div>
                    <div class="h4 mb-0">{{ number_format($totals['revenue'], 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Today Calls / Chats</div>
                    <div class="h4 mb-0">{{ $totals['today_calls'] }} / {{ $totals['today_chats'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h6 class="text-muted">Weekly Activity</h6>
            <canvas id="activityChart" height="90"></canvas>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const ctx = document.getElementById('activityChart');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($chart['labels']),
            datasets: [
                {
                    label: 'Daily',
                    data: @json($chart['daily']),
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.15)',
                    tension: 0.35,
                    fill: true,
                }
            ],
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
            }
        }
    });
</script>
@endpush
