@extends('admin.layouts.app')

@section('title', 'Promo Campaigns')
@section('page_title', 'Promo Campaigns')

@section('content')
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Total Campaigns</h6>
                    <h3 class="mb-0">{{ $stats['total'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Active</h6>
                    <h3 class="mb-0 text-success">{{ $stats['active'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Total Redemptions</h6>
                    <h3 class="mb-0">{{ $stats['redemptions'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Total Discounted</h6>
                    <h3 class="mb-0">₹{{ number_format($stats['total_discounted'], 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">All Campaigns</h5>
            <a href="{{ route('admin.promos.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle"></i> New Campaign
            </a>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <form method="GET" class="row g-2">
                    <div class="col-md-3">
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All Statuses</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="paused" {{ request('status') == 'paused' ? 'selected' : '' }}>Paused</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="type" class="form-select form-select-sm">
                            <option value="">All Types</option>
                            <option value="discount" {{ request('type') == 'discount' ? 'selected' : '' }}>Discount</option>
                            <option value="cashback" {{ request('type') == 'cashback' ? 'selected' : '' }}>Cashback</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-sm btn-outline-primary">Filter</button>
                        <a href="{{ route('admin.promos.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Value</th>
                            <th>Usage</th>
                            <th>Status</th>
                            <th>Valid Until</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($campaigns as $campaign)
                            <tr>
                                <td><code>{{ $campaign->code }}</code></td>
                                <td>{{ $campaign->name }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ ucfirst($campaign->type) }}</span>
                                </td>
                                <td>
                                    @if($campaign->discount_type == 'percent')
                                        {{ $campaign->discount_value }}%
                                    @else
                                        ₹{{ $campaign->discount_value }}
                                    @endif
                                </td>
                                <td>
                                    {{ $campaign->redemptions_count }} /
                                    {{ $campaign->max_total_usage ?? '∞' }}
                                </td>
                                <td>
                                    @if($campaign->status == 'active')
                                        <span class="badge bg-success">Active</span>
                                    @elseif($campaign->status == 'paused')
                                        <span class="badge bg-warning">Paused</span>
                                    @else
                                        <span class="badge bg-secondary">Expired</span>
                                    @endif
                                </td>
                                <td>
                                    @if($campaign->valid_until)
                                        {{ $campaign->valid_until->format('d M Y') }}
                                    @else
                                        <span class="text-muted">No expiry</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.promos.show', $campaign) }}" class="btn btn-outline-primary"
                                            title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.promos.edit', $campaign) }}" class="btn btn-outline-secondary"
                                            title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.promos.toggle', $campaign) }}"
                                            class="d-inline">
                                            @csrf
                                            <button type="submit"
                                                class="btn btn-outline-{{ $campaign->status == 'active' ? 'warning' : 'success' }}"
                                                title="{{ $campaign->status == 'active' ? 'Pause' : 'Activate' }}">
                                                <i
                                                    class="bi bi-{{ $campaign->status == 'active' ? 'pause' : 'play' }}-circle"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No campaigns found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $campaigns->links() }}
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
@endpush