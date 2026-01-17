@extends('admin.layouts.app')

@section('title', 'Payment History')

@section('content')
    <div class="row mb-4">
        <!-- Stats Cards -->
        <div class="col-md-3">
            <div class="card shadow-sm border-start-primary h-100 rounded-4">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">Total Volume</div>
                    <h3 class="mb-0 fw-bold">₹{{ number_format($aggregates['total_volume'], 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-start-success h-100 rounded-4">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">Successful</div>
                    <h3 class="mb-0 text-success fw-bold">{{ number_format($aggregates['success_count']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-start-danger h-100 rounded-4">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">Failed</div>
                    <h3 class="mb-0 text-danger fw-bold">{{ number_format($aggregates['failed_count']) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <x-admin.filter-bar :action="route('admin.payments.index')" :filters="['date', 'status']" />

    <x-admin.table :columns="['Order ID', 'Date', 'User', 'Amount', 'Type', 'Method', 'Status', 'Actions']"
        :rows="$payments">
        @forelse($payments as $payment)
            <tr>
                <td class="ps-4 font-monospace small text-muted">{{ Str::limit($payment->order_id, 10, '...') }}</td>
                <td>
                    <div class="fw-bold text-dark">{{ $payment->created_at->format('M d') }}</div>
                    <div class="small text-muted">{{ $payment->created_at->format('H:i') }}</div>
                </td>
                <td>
                    @if($payment->user)
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle-sm bg-light text-secondary me-2 rounded-circle d-flex align-items-center justify-content-center"
                                style="width:30px;height:30px;">
                                <i class="fas fa-user"></i>
                            </div>
                            <a href="{{ route('admin.users.show', $payment->user_id) }}"
                                class="text-decoration-none fw-bold text-dark">{{ $payment->user->name }}</a>
                        </div>
                    @else
                        <span class="text-muted fst-italic">Deleted User</span>
                    @endif
                </td>
                <td class="text-success fw-bold">₹{{ number_format($payment->amount, 2) }}</td>
                <td>
                    <span class="badge bg-light text-dark border">{{ ucfirst(str_replace('_', ' ', $payment->type)) }}</span>
                </td>
                <td>
                    <span class="small text-muted">{{ $payment->payment_method ?? 'N/A' }}</span>
                </td>
                <td>
                    @php
                        $badgeClass = match ($payment->status) {
                            'success' => 'success-subtle text-success',
                            'failed' => 'danger-subtle text-danger',
                            'pending' => 'warning-subtle text-warning',
                            default => 'light text-dark border'
                        };
                    @endphp
                    <span class="badge bg-{{ $badgeClass }} rounded-pill px-3">{{ ucfirst($payment->status) }}</span>
                </td>
                <td class="text-end pe-4">
                    <a href="{{ route('admin.payments.show', $payment->id) }}" class="btn btn-sm btn-light rounded-circle"
                        data-bs-toggle="tooltip" title="View Details">
                        <i class="fas fa-eye text-primary"></i>
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center py-5">
                    <div class="text-muted mb-2"><i class="fas fa-credit-card fa-3x opacity-25"></i></div>
                    <p class="text-muted">No payment records found.</p>
                </td>
            </tr>
        @endforelse
    </x-admin.table>
@endsection