@extends('admin.layouts.app')

@section('title', 'Wallet Ledger - ' . $user->name)

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="fw-bold m-0">{{ $user->name }}</h2>
                <div class="text-muted small">{{ $user->email }} | {{ $user->phone }}</div>
            </div>
            <div class="d-flex gap-2">
                @can('wallet_adjustments')
                    <button class="btn btn-primary rounded-pill px-4" onclick="openAdjustModal()">
                        <i class="fas fa-coins me-1"></i> Adjust Wallet
                    </button>
                @endcan
                <a href="{{ route('admin.finance.wallets.index') }}" class="btn btn-light rounded-pill px-4">Back to Ledger</a>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <div class="text-muted small">Current Balance</div>
                        <div class="fw-bold fs-4">INR {{ number_format($user->wallet_balance ?? 0, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <div class="text-muted small">Cash Bucket</div>
                        <div class="fw-bold fs-5">INR {{ number_format((float) ($bucketTotals['cash'] ?? 0), 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <div class="text-muted small">Bonus Bucket</div>
                        <div class="fw-bold fs-5">INR {{ number_format((float) ($bucketTotals['bonus'] ?? 0), 2) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.finance.wallets.show', $user->id) }}" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small text-muted">Type</label>
                        <select name="type" class="form-select form-select-sm">
                            <option value="">All</option>
                            @foreach($typeOptions as $key => $label)
                                <option value="{{ $key }}" {{ ($filters['type'] ?? '') === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted">Source</label>
                        <input type="text" name="source" class="form-control form-control-sm"
                            value="{{ $filters['source'] ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted">Start Date (IST)</label>
                        <input type="date" name="start_date" class="form-control form-control-sm"
                            value="{{ $filters['start_date'] ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted">End Date (IST)</label>
                        <input type="date" name="end_date" class="form-control form-control-sm"
                            value="{{ $filters['end_date'] ?? '' }}">
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button class="btn btn-primary btn-sm rounded-pill px-4" type="submit">Apply</button>
                        <a class="btn btn-light btn-sm rounded-pill px-4" href="{{ route('admin.finance.wallets.show', $user->id) }}">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <x-admin.table :columns="['Date (IST)', 'Amount', 'Type', 'Source', 'Reference', 'Balance After', 'Description']" :rows="$transactions">
            @forelse($transactions as $txn)
                @php
                    $ts = $txn->created_at?->setTimezone('Asia/Kolkata');
                    $amountValue = $txn->type === 'debit' ? -abs($txn->amount) : abs($txn->amount);
                    $amountClass = $amountValue >= 0 ? 'text-success' : 'text-danger';
                @endphp
                <tr>
                    <td class="ps-4">
                        <div class="fw-bold">{{ $ts?->format('d M Y') }}</div>
                        <div class="small text-muted">{{ $ts?->format('H:i') }}</div>
                    </td>
                    <td class="fw-bold {{ $amountClass }}">INR {{ number_format($amountValue, 2) }}</td>
                    <td>{{ ucfirst($txn->type) }}</td>
                    <td>{{ $txn->source ?? '-' }}</td>
                    <td class="font-monospace small">
                        {{ $txn->reference_type ?? '-' }}
                        @if($txn->reference_id)
                            <div class="text-muted">#{{ $txn->reference_id }}</div>
                        @endif
                    </td>
                    <td>INR {{ number_format($txn->balance_after ?? 0, 2) }}</td>
                    <td>{{ $txn->description ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">No ledger entries for this user.</td>
                </tr>
            @endforelse
        </x-admin.table>
    </div>

    @can('wallet_adjustments')
        <div class="modal fade" id="adjustModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow rounded-4">
                    <div class="modal-header border-0">
                        <h5 class="modal-title fw-bold">Wallet Adjustment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="alert alert-warning border-0 rounded-3">
                            Double-check the amount and reason before confirming.
                        </div>

                        <form id="adjustForm" method="POST" action="{{ route('admin.finance.wallets.adjust', $user->id) }}">
                            @csrf
                            <input type="hidden" name="idempotency_key" id="idempotencyKey">

                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="type" id="typeCredit" value="credit" checked>
                                    <label class="btn btn-outline-success w-100 rounded-pill" for="typeCredit">Credit</label>
                                </div>
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="type" id="typeDebit" value="debit">
                                    <label class="btn btn-outline-danger w-100 rounded-pill" for="typeDebit">Debit</label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Amount (INR)</label>
                                <input type="number" name="amount" class="form-control form-control-lg" min="1" step="0.01" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Reason</label>
                                <textarea name="reason" class="form-control" rows="2" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Reference ID (Optional)</label>
                                <input type="text" name="reference_id" class="form-control">
                            </div>

                            <div id="reviewSection" class="border rounded-3 p-3 mb-3 d-none">
                                <div class="small text-muted">Review</div>
                                <div>Before: <span id="previewBefore">0.00</span></div>
                                <div>After: <span id="previewAfter">0.00</span></div>
                                <div>Adjustment: <span id="previewAmount">0.00</span></div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" onclick="prepareAdjustment()">Review</button>
                                <button type="submit" id="confirmButton" class="btn btn-primary rounded-pill px-4" disabled>Confirm</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endcan

    @push('scripts')
        <script>
            function generateUUID() {
                return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
                    const r = Math.random() * 16 | 0;
                    const v = c === 'x' ? r : (r & 0x3 | 0x8);
                    return v.toString(16);
                });
            }

            function openAdjustModal() {
                document.getElementById('idempotencyKey').value = generateUUID();
                document.getElementById('confirmButton').disabled = true;
                document.getElementById('reviewSection').classList.add('d-none');
                new bootstrap.Modal(document.getElementById('adjustModal')).show();
            }

            function prepareAdjustment() {
                const amountInput = document.querySelector('#adjustForm input[name="amount"]');
                const typeInput = document.querySelector('#adjustForm input[name="type"]:checked');
                const amount = parseFloat(amountInput.value || '0');
                const before = parseFloat('{{ number_format($user->wallet_balance ?? 0, 2, '.', '') }}');

                if (!amount || amount <= 0) {
                    alert('Enter a valid amount to preview.');
                    return;
                }

                const isCredit = typeInput && typeInput.value === 'credit';
                const after = isCredit ? before + amount : before - amount;
                const delta = isCredit ? amount : -amount;

                document.getElementById('previewBefore').innerText = before.toFixed(2);
                document.getElementById('previewAfter').innerText = after.toFixed(2);
                document.getElementById('previewAmount').innerText = delta.toFixed(2);
                document.getElementById('reviewSection').classList.remove('d-none');
                document.getElementById('confirmButton').disabled = false;
            }
        </script>
    @endpush
@endsection
