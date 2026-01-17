@extends('admin.layouts.app')

@section('title', 'Manage Wallets')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold m-0 text-dark">User Wallets</h2>
            <p class="text-muted mb-0">Monitor balances and perform manual adjustments.</p>
        </div>
    </div>

    <x-admin.filter-bar :action="route('admin.wallets.index')" :filters="['search', 'status']" />

    <x-admin.table :columns="['User', 'Current Balance', 'Last Transaction', 'Status', 'Actions']" :rows="$users">
        @forelse($users as $user)
            <tr>
                <td class="ps-4">
                    <div class="d-flex align-items-center">
                        <div class="avatar-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-3"
                            style="width: 40px; height: 40px;">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div>
                            <div class="fw-bold text-dark">{{ $user->name }}</div>
                            <div class="small text-muted">{{ $user->email }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="fw-bold fs-6 text-dark">₹{{ number_format($user->wallet_balance, 2) }}</div>
                </td>
                <td>
                    @if($user->latestWalletTransaction)
                        <div class="small text-dark">{{ $user->latestWalletTransaction->description }}</div>
                        <div class="small text-muted">{{ $user->latestWalletTransaction->created_at->diffForHumans() }}</div>
                    @else
                        <span class="text-muted small">-</span>
                    @endif
                </td>
                <td>
                    @if($user->is_active)
                        <span class="badge bg-success-subtle text-success rounded-pill px-3">Active</span>
                    @else
                        <span class="badge bg-danger-subtle text-danger rounded-pill px-3">Banned</span>
                    @endif
                </td>
                <td class="text-end pe-4">
                    <button class="btn btn-sm btn-outline-primary rounded-pill me-2"
                        onclick="openRechargeModal({{ $user->id }}, '{{ $user->name }}', {{ $user->wallet_balance }})">
                        <i class="fas fa-coins me-1"></i> Adjust
                    </button>
                    <a href="{{ route('admin.wallets.show', $user->id) }}"
                        class="btn btn-sm btn-light rounded-circle text-primary">
                        <i class="fas fa-history"></i>
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center py-5">
                    <p class="text-muted">No wallets found.</p>
                </td>
            </tr>
        @endforelse
    </x-admin.table>

    <!-- Recharge Modal -->
    <div class="modal fade" id="rechargeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Wallet Adjustment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-warning border-0 rounded-3 d-flex align-items-start mb-3">
                        <i class="fas fa-exclamation-triangle mt-1 me-3"></i>
                        <div class="small">
                            <strong>Warning:</strong> This action involves real money/credits and cannot be automatically
                            reversed. Please verify the amount and reason carefully.
                        </div>
                    </div>

                    <form id="rechargeForm" method="POST">
                        @csrf
                        <input type="hidden" name="idempotency_key" id="idempotencyKey">

                        <div class="text-center mb-4">
                            <div class="avatar-circle bg-light text-primary mx-auto mb-2 d-flex align-items-center justify-content-center"
                                style="width: 60px; height: 60px; font-size: 1.5rem;">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <h5 class="fw-bold mb-1" id="modalUserName">User Name</h5>
                            <div class="text-muted small">Current Balance: ₹<span id="modalUserBalance">0.00</span></div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="type" id="typeCredit" value="credit" checked>
                                <label class="btn btn-outline-success w-100 rounded-pill" for="typeCredit">
                                    <i class="fas fa-plus-circle me-1"></i> Credit (Add)
                                </label>
                            </div>
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="type" id="typeDebit" value="debit">
                                <label class="btn btn-outline-danger w-100 rounded-pill" for="typeDebit">
                                    <i class="fas fa-minus-circle me-1"></i> Debit (Deduct)
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Amount (₹)</label>
                            <input type="number" name="amount" class="form-control form-control-lg fw-bold"
                                placeholder="0.00" min="1" step="0.01" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Reason / Description</label>
                            <textarea name="reason" class="form-control" rows="2" placeholder="e.g. Refund for Call #1234"
                                required minlength="5"></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary rounded-pill py-2 fw-bold">Confirm
                                Adjustment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function generateUUID() {
                return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
                    var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
                    return v.toString(16);
                });
            }

            function openRechargeModal(userId, userName, currentBalance) {
                document.getElementById('modalUserName').innerText = userName;
                document.getElementById('modalUserBalance').innerText = currentBalance.toFixed(2);
                document.getElementById('rechargeForm').action = `/admin/wallets/${userId}/recharge`;

                // Generate new Idempotency Key for this "open" session
                document.getElementById('idempotencyKey').value = generateUUID();

                new bootstrap.Modal(document.getElementById('rechargeModal')).show();
            }
        </script>
    @endpush
@endsection