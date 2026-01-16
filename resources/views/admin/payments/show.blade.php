@extends('admin.layouts.app')

@section('title', 'Payment Details')

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Order Summary</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted d-block small">Status</label>
                        <span
                            class="badge bg-{{ $order->status === 'success' ? 'success' : ($order->status === 'failed' ? 'danger' : 'warning') }} fs-6">
                            {{ strtoupper($order->status) }}
                        </span>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted d-block small">Amount</label>
                        <h3 class="fw-bold text-primary">₹{{ number_format($order->amount, 2) }}</h3>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted d-block small">User</label>
                        <div class="fw-bold">{{ $order->user->name }}</div>
                        <div class="small">{{ $order->user->email }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted d-block small">Merchant Txn ID</label>
                        <code>{{ $order->merchant_transaction_id }}</code>
                    </div>
                    @if($order->provider_transaction_id)
                        <div class="mb-3">
                            <label class="text-muted d-block small">PhonePe Txn ID</label>
                            <code>{{ $order->provider_transaction_id }}</code>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Webhook Timeline</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Time</th>
                                <th>Status Detail</th>
                                <th>Signature</th>
                                <th>Processing</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($webhooks as $hook)
                                <tr>
                                    <td>{{ $hook->created_at->format('H:i:s d M') }}</td>
                                    <td>
                                        <code>{{ $hook->payload['code'] ?? 'N/A' }}</code>
                                    </td>
                                    <td>
                                        @if($hook->signature_valid)
                                            <span class="text-success"><i class="fas fa-check-circle"></i> Valid</span>
                                        @else
                                            <span class="text-danger"><i class="fas fa-times-circle"></i> Invalid</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($hook->processing_status === 'processed')
                                            <span class="badge bg-success">Processed</span>
                                        @elseif($hook->processing_status === 'failed')
                                            <span class="badge bg-danger" title="{{ $hook->error_message }}">Failed</span>
                                        @else
                                            <span class="badge bg-secondary">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No webhooks received yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($order->meta)
                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-white">
                        <h6 class="mb-0">Raw Metadata</h6>
                    </div>
                    <div class="card-body bg-light">
                        <pre class="small mb-0"
                            style="max-height: 200px; overflow-y:auto;">{{ json_encode($order->meta, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection