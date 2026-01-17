@extends('admin.layouts.app')

@section('title', 'Payment Details')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="fw-bold m-0">Payment Details</h2>
                <div class="text-muted small">Order and webhook processing timeline</div>
            </div>
            <a href="{{ route('admin.finance.payments.index') }}" class="btn btn-light rounded-pill px-4">Back to Payments</a>
        </div>

        @php
            $status = strtolower($paymentOrder->status ?? '');
            $badge = in_array($status, ['success', 'completed', 'paid'], true) ? 'success' : (in_array($status, ['failed', 'expired'], true) ? 'danger' : 'warning');
            $createdAt = $paymentOrder->created_at?->setTimezone('Asia/Kolkata');
            $updatedAt = $paymentOrder->updated_at?->setTimezone('Asia/Kolkata');
        @endphp

        <div class="row g-4 mb-4">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Payment Summary</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="text-muted small">Merchant Txn ID</div>
                                <div class="fw-bold font-monospace">{{ $paymentOrder->merchant_transaction_id }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Provider Txn ID</div>
                                <div class="fw-bold font-monospace">{{ $paymentOrder->provider_transaction_id ?? '-' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Amount</div>
                                <div class="fw-bold">INR {{ number_format($paymentOrder->amount, 2) }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Status</div>
                                <div><span class="badge bg-{{ $badge }} rounded-pill px-3">{{ strtoupper($paymentOrder->status ?? '-') }}</span></div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Created (IST)</div>
                                <div class="fw-bold">{{ $createdAt?->format('d M Y, H:i') }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Last Updated (IST)</div>
                                <div class="fw-bold">{{ $updatedAt?->format('d M Y, H:i') }}</div>
                            </div>
                        </div>
                        @can('manage_payments')
                            <div class="d-flex gap-2 mt-4 flex-wrap">
                                <form method="POST" action="{{ route('admin.finance.payments.recheck', $paymentOrder->id) }}">
                                    @csrf
                                    <button class="btn btn-outline-secondary btn-sm rounded-pill px-4" type="submit"
                                        onclick="return confirm('Queue a status recheck for this payment?')">Re-check Status</button>
                                </form>
                                <form method="POST" action="{{ route('admin.finance.payments.retry_webhook', $paymentOrder->id) }}">
                                    @csrf
                                    <button class="btn btn-outline-secondary btn-sm rounded-pill px-4" type="submit"
                                        onclick="return confirm('Retry webhook processing for this payment?')">Retry Webhook</button>
                                </form>
                            </div>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">User</h5>
                        @if($paymentOrder->user)
                            <div class="fw-bold">{{ $paymentOrder->user->name }}</div>
                            <div class="text-muted small">{{ $paymentOrder->user->email }}</div>
                            <div class="text-muted small">{{ $paymentOrder->user->phone }}</div>
                        @else
                            <div class="text-muted">Deleted User</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Admin Note</h5>
                        @can('manage_payments')
                            <form method="POST" action="{{ route('admin.finance.payments.note', $paymentOrder->id) }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small text-muted">Status</label>
                                    <select name="admin_note_status" class="form-select form-select-sm">
                                        <option value="">None</option>
                                        <option value="investigating" {{ $paymentOrder->admin_note_status === 'investigating' ? 'selected' : '' }}>Investigating</option>
                                        <option value="cleared" {{ $paymentOrder->admin_note_status === 'cleared' ? 'selected' : '' }}>Cleared</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small text-muted">Note</label>
                                    <textarea name="admin_note" rows="3" class="form-control" placeholder="Add context for finance follow-up">{{ $paymentOrder->admin_note }}</textarea>
                                </div>
                                <button class="btn btn-primary btn-sm rounded-pill px-4" type="submit">Save Note</button>
                            </form>
                        @else
                            <div class="text-muted small">Status: {{ $paymentOrder->admin_note_status ?? 'None' }}</div>
                            <div class="mt-2">{{ $paymentOrder->admin_note ?? 'No note yet.' }}</div>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Wallet Credit</h5>
                        @if($walletCredit)
                            <div class="text-muted small">Transaction ID</div>
                            <div class="fw-bold font-monospace mb-2">#{{ $walletCredit->id }}</div>
                            <div class="text-muted small">Amount</div>
                            <div class="fw-bold mb-2">INR {{ number_format($walletCredit->amount, 2) }}</div>
                            <div class="text-muted small">Credited At (IST)</div>
                            <div class="fw-bold">
                                {{ $walletCredit->created_at?->setTimezone('Asia/Kolkata')->format('d M Y, H:i') }}
                            </div>
                        @else
                            <div class="text-muted">No wallet credit recorded for this payment.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Webhook Events ({{ $webhooks->count() }})</h5>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Event ID</th>
                                <th>Status</th>
                                <th>Processed At</th>
                                <th>Created At</th>
                                <th>Error</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($webhooks as $event)
                                <tr>
                                    <td class="font-monospace small">{{ $event->id }}</td>
                                    <td>{{ ucfirst($event->processing_status) }}</td>
                                    <td>{{ $event->processed_at?->setTimezone('Asia/Kolkata')->format('d M Y, H:i') ?? '-' }}</td>
                                    <td>{{ $event->created_at?->setTimezone('Asia/Kolkata')->format('d M Y, H:i') ?? '-' }}</td>
                                    <td class="text-muted small">{{ $event->error_message ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No webhook events logged yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Order Meta (Sanitized)</h5>
                        <pre class="bg-light p-3 rounded-3 small">{{ json_encode($meta, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Latest Webhook Payload (Sanitized)</h5>
                        <pre class="bg-light p-3 rounded-3 small">{{ json_encode($latestWebhookPayload, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
