@extends('layouts.astrologer')

@section('title', 'Earnings')
@section('page-title', 'My Earnings')

@section('content')
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card card-premium bg-primary text-white border-0 h-100 position-relative overflow-hidden">
                <div class="card-body p-4 position-relative z-1">
                    <div class="small text-uppercase opacity-75 fw-bold">Total Earnings</div>
                    <h2 class="mb-0 fw-bold display-6 mt-2">₹ {{ number_format($totalEarnings, 2) }}</h2>
                    <div class="mt-3 small opacity-75">
                        <i class="fas fa-check-circle me-1"></i> Lifetime verified earnings
                    </div>
                </div>
                <!-- Decorative circle -->
                <div class="position-absolute top-0 end-0 rounded-circle bg-white opacity-10"
                    style="width: 150px; height: 150px; margin-right: -50px; margin-top: -50px;"></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-premium border-0 h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="small text-uppercase text-muted fw-bold">This Month</div>
                            <h3 class="mb-0 fw-bold mt-2 text-dark">₹ {{ number_format($monthEarnings, 2) }}</h3>
                        </div>
                        <div class="rounded-circle bg-success-subtle p-3 text-success">
                            <i class="fas fa-calendar-alt fa-lg"></i>
                        </div>
                    </div>
                    <div class="mt-3 small text-muted">
                        Generic performance message or trend
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-premium border-0 h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="small text-uppercase text-muted fw-bold">Today</div>
                            <h3 class="mb-0 fw-bold mt-2 text-dark">₹ {{ number_format($todayEarnings, 2) }}</h3>
                        </div>
                        <div class="rounded-circle bg-warning-subtle p-3 text-warning">
                            <i class="fas fa-coins fa-lg"></i>
                        </div>
                    </div>
                    <div class="mt-3 small text-muted">
                        Updates in real-time
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-premium mb-4">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">Transaction History</h5>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal"
                    data-bs-target="#withdrawalModal">
                    <i class="fas fa-hand-holding-usd me-1"></i> Request Payout
                </button>
                <button class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                    <i class="fas fa-download me-1"></i> Export
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-uppercase small text-muted">
                        <tr>
                            <th class="ps-4 border-0">Date</th>
                            <th class="border-0">Source</th>
                            <th class="border-0">Reference ID</th>
                            <th class="border-0">Amount</th>
                            <th class="border-0 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ledger as $entry)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">{{ $entry->created_at->format('M d, Y') }}</div>
                                    <div class="small text-muted">{{ $entry->created_at->format('h:i A') }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ ucfirst($entry->source) }}</span>
                                </td>
                                <td class="font-monospace small text-primary">
                                    {{ substr($entry->reference_id, 0, 8) }}...
                                </td>
                                <td>
                                    <span class="fw-bold {{ $entry->amount > 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $entry->amount > 0 ? '+' : '' }} ₹ {{ number_format(abs($entry->amount), 2) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($entry->status == 'completed' || $entry->status == 'settled')
                                        <i class="fas fa-check-circle text-success" title="Settled"></i>
                                    @elseif($entry->status == 'cancelled' || $entry->status == 'rejected')
                                        <i class="fas fa-times-circle text-danger" title="Rejected"></i>
                                    @else
                                        <i class="fas fa-hourglass-half text-warning" title="Pending"></i>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">No transactions found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($ledger->hasPages())
                <div class="p-4 border-top">
                    {{ $ledger->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Withdrawal Modal -->
    <div class="modal fade" id="withdrawalModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Request Payout</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('withdrawals.store') }}" method="POST">
                        @csrf
                        <div class="alert alert-info border-0 d-flex align-items-center mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <div class="small">Minimum withdrawal amount is ₹500.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Amount (₹)</label>
                            <input type="number" name="amount" class="form-control" min="500" step="10" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted">Bank Details / UPI ID</label>
                            <textarea name="bank_details" class="form-control" rows="3"
                                placeholder="Enter Account No, IFSC, or UPI ID" required></textarea>
                            <div class="form-text">Ensure details are correct. We are not responsible for wrong transfers.
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary py-2 rounded-pill">Submit Request</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection