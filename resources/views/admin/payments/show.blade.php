@extends('admin.layouts.app')

@section('title', 'Transaction Details #' . Str::limit($order->id, 8, ''))
@section('page_title', 'Transaction Detail')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <!-- Order Details -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Order Information</h5>
                    <span
                        class="badge bg-{{ $order->status == 'completed' ? 'success' : ($order->status == 'failed' ? 'danger' : 'warning') }} fs-6">{{ ucfirst($order->status) }}</span>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6 border-end">
                            <label class="text-muted small text-uppercase">Customer</label>
                            @if($order->user)
                                <div class="d-flex align-items-center mt-2">
                                    <div class="avatar-circle me-3 bg-secondary text-white d-flex align-items-center justify-content-center"
                                        style="width: 40px; height: 40px; border-radius: 50%;">
                                        {{ strtoupper(substr($order->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold"><a href="{{ route('admin.users.show', $order->user_id) }}"
                                                class="text-dark text-decoration-none">{{ $order->user->name }}</a></div>
                                        <div class="small text-muted">{{ $order->user->email }}</div>
                                        <div class="small text-muted">{{ $order->user->phone }}</div>
                                    </div>
                                </div>
                            @else
                                <div class="text-danger mt-2">Unknown User (Deleted)</div>
                            @endif
                        </div>
                        <div class="col-md-6 ps-4">
                            <label class="text-muted small text-uppercase">Payment Info</label>
                            <div class="mt-2">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Amount:</span>
                                    <span class="fw-bold fs-5">{{ number_format($order->amount, 2) }}
                                        {{ $order->currency }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Provider:</span>
                                    <span>{{ ucfirst($order->provider) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Created:</span>
                                    <span>{{ $order->created_at->format('M d, Y H:i:s') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <h6 class="text-muted text-uppercase mb-3 small">Gateway References</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-0">
                            <tbody>
                                <tr>
                                    <th class="bg-light w-25">Order ID (Check)</th>
                                    <td class="font-monospace">{{ $order->id }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Merchant Trans ID</th>
                                    <td class="font-monospace">{{ $order->merchant_transaction_id }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Provider Trans ID</th>
                                    <td class="font-monospace">{{ $order->provider_transaction_id ?? 'N/A' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    @if($order->meta)
                        <div class="mt-4">
                            <h6 class="text-muted text-uppercase mb-2 small">Raw Metadata</h6>
                            <pre
                                class="bg-light p-3 border rounded small text-muted">{{ json_encode($order->meta, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Webhook Logs -->
            @if(isset($webhooks) && $webhooks->count() > 0)
                <div class="card shadow-sm">
                    <div class="card-header bg-light">Webhook Events</div>
                    <div class="table-responsive">
                        <table class="table table-striped mb-0 small">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Event Type</th>
                                    <th>Status Code</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($webhooks as $hook)
                                    <tr>
                                        <td>{{ $hook->created_at->format('H:i:s') }}</td>
                                        <td>{{ $hook->event_type }}</td>
                                        <td><span class="badge bg-secondary">{{ $hook->status_code ?? 200 }}</span></td>
                                        <td>
                                            <button class="btn btn-xs btn-link p-0" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#hook{{ $hook->id }}">Payload</button>
                                            <div class="collapse mt-1" id="hook{{ $hook->id }}">
                                                <pre class="mb-0"
                                                    style="font-size: 0.7rem;">{{ json_encode($hook->payload, JSON_PRETTY_PRINT) }}</pre>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <!-- Actions Sidebar -->
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white fw-bold">Actions</div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-secondary" disabled>Sync Status (Auto)</button>
                        @if($order->status == 'completed')
                            <button class="btn btn-outline-danger"
                                onclick="alert('Refund implementation pending payment gateway service integration')">Initiate
                                Refund</button>
                        @endif
                    </div>
                    <div class="alert alert-light border small mt-3 mb-0">
                        <i class="fas fa-info-circle me-1"></i> Refunds in test mode will not process actual money.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection