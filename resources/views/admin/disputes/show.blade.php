@extends('admin.layouts.app')

@section('title', 'Dispute #' . substr($dispute->id, 0, 8))
@section('page_title', 'Dispute Review')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <!-- Transaction Context -->
            <div class="card mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0">📋 Transaction Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Service Type</h6>
                            <p class="fs-5"><span
                                    class="badge bg-primary">{{ class_basename($dispute->reference_type) }}</span></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Transaction ID</h6>
                            <p><code>{{ $dispute->reference_id }}</code></p>
                        </div>
                    </div>
                    @if($transactionDetails)
                        <table class="table table-sm">
                            @foreach($transactionDetails as $key => $value)
                                <tr>
                                    <th width="180">{{ ucfirst(str_replace('_', ' ', $key)) }}:</th>
                                    <td>{{ $value }}</td>
                                </tr>
                            @endforeach
                        </table>
                    @endif
                </div>
            </div>

            <!-- Dispute Details -->
            <div class="card mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0">⚠️ Dispute Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted">Reason</h6>
                            <span class="badge bg-danger">{{ str_replace('_', ' ', ucfirst($dispute->reason_code)) }}</span>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Requested Refund</h6>
                            <p class="fs-5 fw-bold text-danger mb-0">
                                ₹{{ number_format($dispute->requested_refund_amount, 2) }}</p>
                        </div>
                    </div>
                    @if($dispute->description)
                        <div>
                            <h6 class="text-muted">Description</h6>
                            <p class="border-start border-3 ps-3">{{ $dispute->description }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Timeline -->
            <div class="card mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0">📅 Timeline</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach($dispute->events()->orderBy('created_at')->get() as $event)
                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                        style="width: 32px; height: 32px;">
                                        <i class="bi bi-circle-fill" style="font-size: 8px;"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="fw-bold">{{ str_replace('_', ' ', ucfirst($event->event_type)) }}</div>
                                    <small class="text-muted">{{ $event->created_at->format('d M Y, H:i') }}
                                        ({{ $event->created_at->diffForHumans() }})</small>
                                    @if($event->meta_json)
                                        <div class="small mt-1">
                                            @foreach($event->meta_json as $key => $value)
                                                <div><strong>{{ ucfirst($key) }}:</strong> {{ $value }}</div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            @if($dispute->admin_notes)
                <div class="card mb-3">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">📝 Admin Notes</h5>
                    </div>
                    <div class="card-body">
                        <p>{{ $dispute->admin_notes }}</p>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-4">
            <!-- User Info -->
            <div class="card mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0">👤 User Information</h6>
                </div>
                <div class="card-body">
                    <p><strong>Name:</strong> {{ $dispute->user->name }}</p>
                    <p><strong>Phone:</strong> {{ $dispute->user->phone }}</p>
                    <p><strong>Email:</strong> {{ $dispute->user->email ?? 'N/A' }}</p>
                    <p class="mb-0"><strong>Member Since:</strong> {{ $dispute->user->created_at->format('d M Y') }}</p>
                </div>
            </div>

            <!-- Status Card -->
            <div class="card mb-3">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Current Status</h6>
                    @if($dispute->status == 'submitted')
                        <h4 class="badge bg-warning text-dark">New Dispute</h4>
                    @elseif($dispute->status == 'under_review')
                        <h4 class="badge bg-info">Under Review</h4>
                    @elseif($dispute->status == 'approved_full')
                        <h4 class="badge bg-success">Approved (Full)</h4>
                    @elseif($dispute->status == 'approved_partial')
                        <h4 class="badge bg-success">Approved (Partial)</h4>
                    @elseif($dispute->status == 'rejected')
                        <h4 class="badge bg-danger">Rejected</h4>
                    @else
                        <h4 class="badge bg-secondary">{{ ucfirst($dispute->status) }}</h4>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            @if(!in_array($dispute->status, ['approved_full', 'approved_partial', 'rejected', 'closed']))
                <div class="card">
                    <div class="card-header bg-white">
                        <h6 class="mb-0">💰 Refund Actions</h6>
                    </div>
                    <div class="card-body">
                        <!-- Request More Info -->
                        <form method="POST" action="{{ route('admin.disputes.request_info', $dispute) }}" class="mb-3">
                            @csrf
                            <label class="form-label small">Request More Info</label>
                            <textarea name="message" class="form-control form-control-sm mb-2" rows="2"
                                placeholder="What additional information do you need?"></textarea>
                            <button type="submit" class="btn btn-sm btn-warning w-100">
                                <i class="bi bi-question-circle"></i> Request Info
                            </button>
                        </form>

                        <hr>

                        <!-- Approve Refund -->
                        <form method="POST" action="{{ route('admin.disputes.approve', $dispute) }}" id="approveForm">
                            @csrf
                            <label class="form-label small">Approve Refund</label>
                            <div class="input-group input-group-sm mb-2">
                                <span class="input-group-text">₹</span>
                                <input type="number" step="0.01" name="amount" id="refundAmount" class="form-control"
                                    value="{{ $dispute->requested_refund_amount }}"
                                    max="{{ $dispute->requested_refund_amount }}" required>
                            </div>
                            <div class="d-flex gap-1 mb-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm flex-fill"
                                    onclick="document.getElementById('refundAmount').value = {{ $dispute->requested_refund_amount }}">100%</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm flex-fill"
                                    onclick="document.getElementById('refundAmount').value = {{ $dispute->requested_refund_amount * 0.75 }}">75%</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm flex-fill"
                                    onclick="document.getElementById('refundAmount').value = {{ $dispute->requested_refund_amount * 0.50 }}">50%</button>
                            </div>
                            <textarea name="reason" class="form-control form-control-sm mb-2" rows="2"
                                placeholder="Reason for approval" required></textarea>
                            <button type="submit" class="btn btn-sm btn-success w-100">
                                <i class="bi bi-check-circle"></i> Approve & Issue Refund
                            </button>
                        </form>

                        <hr>

                        <!-- Reject -->
                        <form method="POST" action="{{ route('admin.disputes.reject', $dispute) }}">
                            @csrf
                            <label class="form-label small">Reject Dispute</label>
                            <textarea name="reason" class="form-control form-control-sm mb-2" rows="2"
                                placeholder="Reason for rejection" required></textarea>
                            <button type="submit" class="btn btn-sm btn-danger w-100"
                                onclick="return confirm('Are you sure you want to reject this dispute?')">
                                <i class="bi bi-x-circle"></i> Reject Dispute
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            @if($dispute->refund)
                <div class="card mt-3">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">✅ Refund Issued</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Amount:</strong> ₹{{ number_format($dispute->refund->amount, 2) }}</p>
                        <p><strong>Status:</strong> {{ ucfirst($dispute->refund->status) }}</p>
                        <p class="mb-0"><strong>Issued:</strong> {{ $dispute->refund->created_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .timeline {
            position: relative;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 45px;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.getElementById('approveForm')?.addEventListener('submit', function (e) {
            const amount = document.getElementById('refundAmount').value;
            if (!confirm(`Issue refund of ₹${amount}? This action cannot be undone.`)) {
                e.preventDefault();
            }
        });
    </script>
@endpush
