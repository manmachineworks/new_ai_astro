@extends('admin.layouts.app')

@section('title', 'Disputes')
@section('page_title', 'Dispute Resolution')

@section('content')
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stat-card" style="border-left-color: #dc3545;">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Pending Review</h6>
                    <h3 class="mb-0 text-danger">{{ $stats['pending'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card" style="border-left-color: #198754;">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Approved</h6>
                    <h3 class="mb-0 text-success">{{ $stats['approved'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Total Refunded</h6>
                    <h3 class="mb-0">₹{{ number_format($stats['total_refunded'], 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Avg Response Time</h6>
                    <h3 class="mb-0">{{ $stats['avg_hours'] }}h</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link {{ request('status', 'pending') == 'pending' ? 'active' : '' }}"
                        href="?status=pending">
                        Pending <span class="badge bg-danger">{{ $stats['pending'] }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('status') == 'under_review' ? 'active' : '' }}"
                        href="?status=under_review">
                        Under Review
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('status') == 'needs_info' ? 'active' : '' }}" href="?status=needs_info">
                        Needs Info
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('status') == 'resolved' ? 'active' : '' }}" href="?status=resolved">
                        Resolved
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Service</th>
                            <th>Reason</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($disputes as $dispute)
                            <tr class="{{ $dispute->status == 'submitted' ? 'table-warning' : '' }}">
                                <td><code>#{{ substr($dispute->id, 0, 8) }}</code></td>
                                <td>
                                    <div>{{ $dispute->user->name }}</div>
                                    <small class="text-muted">{{ $dispute->user->phone }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        {{ class_basename($dispute->reference_type) }}
                                    </span>
                                    <br>
                                    <small class="text-muted">#{{ substr($dispute->reference_id, 0, 8) }}</small>
                                </td>
                                <td>
                                    <span
                                        class="badge bg-secondary">{{ str_replace('_', ' ', ucfirst($dispute->reason_code)) }}</span>
                                </td>
                                <td>₹{{ number_format($dispute->requested_refund_amount, 2) }}</td>
                                <td>
                                    @if($dispute->status == 'submitted')
                                        <span class="badge bg-warning">New</span>
                                    @elseif($dispute->status == 'under_review')
                                        <span class="badge bg-info">Review</span>
                                    @elseif($dispute->status == 'approved_full')
                                        <span class="badge bg-success">Approved</span>
                                    @elseif($dispute->status == 'rejected')
                                        <span class="badge bg-danger">Rejected</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $dispute->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div>{{ $dispute->created_at->format('d M Y') }}</div>
                                    <small class="text-muted">{{ $dispute->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <a href="{{ route('admin.disputes.show', $dispute) }}" class="btn btn-sm btn-primary">
                                        Review
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No disputes found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $disputes->links() }}
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
@endpush