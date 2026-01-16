@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row g-4">
            <!-- Sidebar / Profile -->
            <div class="col-lg-4">
                <div class="glass-card mb-4 text-center">
                    <div class="mb-3 position-relative d-inline-block">
                        <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=FFD700&color=000' }}"
                            class="rounded-circle border border-2 border-warning" width="100" height="100">
                        <span
                            class="position-absolute bottom-0 end-0 p-2 {{ $user->is_active ? 'bg-success' : 'bg-danger' }} border border-dark rounded-circle"></span>
                    </div>
                    <h4 class="mb-1">{{ $user->name }}</h4>
                    <p class="text-muted small mb-2">Verified Astrologer</p>
                    
                    <a href="{{ route('astrologer.profile.edit') }}" class="btn btn-sm btn-outline-light rounded-pill mb-3 px-3">
                        <i class="fas fa-pencil me-1"></i> Edit Profile
                    </a>

                    <form action="{{ route('astrologer.toggle-status') }}" method="POST">
                        @csrf
                        <button
                            class="btn btn-sm w-100 rounded-pill {{ $user->is_active ? 'btn-outline-danger' : 'btn-cosmic' }}">
                            {{ $user->is_active ? 'Go Offline' : 'Go Online' }}
                        </button>
                    </form>
                </div>

                <!-- Earnings Card -->
                <div class="glass-card bg-gradient-primary text-center">
                    <h6 class="text-uppercase text-muted small mb-3">Total Earnings</h6>
                    <h2 class="display-5 fw-bold text-gold mb-1">₹ {{ number_format($user->wallet_balance, 2) }}</h2>
                    <p class="text-white-50 small mb-4">Commission (70%) included</p>
                    <div class="d-grid">
                        <button class="btn btn-glass" data-bs-toggle="modal" data-bs-target="#withdrawModal">
                            <i class="fas fa-bank me-2"></i> Request Withdrawal
                        </button>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Stats Row -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="glass-card p-3 d-flex align-items-center">
                            <div class="bg-white-10 rounded-circle p-3 me-3">
                                <i class="fas fa-phone-alt fa-2x text-gold"></i>
                            </div>
                            <div>
                                <h3 class="mb-0">{{ $stats['total_calls'] ?? 0 }}</h3>
                                <small class="text-muted">Total Calls</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="glass-card p-3 d-flex align-items-center">
                            <div class="bg-white-10 rounded-circle p-3 me-3">
                                <i class="fas fa-comments fa-2x text-gold"></i>
                            </div>
                            <div>
                                <h3 class="mb-0">{{ $stats['total_chats'] ?? 0 }}</h3>
                                <small class="text-muted">Total Chats</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Sessions -->
                <div class="glass-card">
                    <h5 class="mb-4">Recent Consultations</h5>
                    <ul class="nav nav-tabs border-bottom-0 mb-3" id="historyTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active text-white" data-bs-toggle="tab" data-bs-target="#calls"
                                type="button">Calls</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link text-white" data-bs-toggle="tab" data-bs-target="#chats"
                                type="button">Chats</button>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="calls">
                            <div class="table-responsive">
                                <table class="table table-dark table-transparent mb-0">
                                    <thead>
                                        <tr class="text-muted small">
                                            <th>Date</th>
                                            <th>Duration</th>
                                            <th class="text-end">Earnings</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentCalls as $call)
                                            <tr>
                                                <td>{{ $call->created_at->format('M d, H:i') }}</td>
                                                <td>{{ gmdate("H:i:s", $call->duration_seconds) }}</td>
                                                <td class="text-end text-success">+ ₹{{ number_format($call->cost * 0.7, 2) }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted py-3">No recent calls</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="chats">
                            <div class="table-responsive">
                                <table class="table table-dark table-transparent mb-0">
                                    <thead>
                                        <tr class="text-muted small">
                                            <th>Date</th>
                                            <th>Duration</th>
                                            <th class="text-end">Earnings</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentChats as $chat)
                                            <tr>
                                                <td>{{ $chat->created_at->format('M d, H:i') }}</td>
                                                <td>{{ $chat->duration_minutes }} min</td>
                                                <td class="text-end text-success">+ ₹{{ number_format($chat->cost * 0.7, 2) }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted py-3">No recent chats</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Withdrawal Modal -->
    <div class="modal fade" id="withdrawModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content glass-card border-0">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Request Payout</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('withdrawals.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-muted">Amount (₹)</label>
                            <input type="number" name="amount" class="form-control form-control-cosmic" min="500"
                                max="{{ $user->wallet_balance }}" required>
                            <small class="text-muted">Available: ₹{{ $user->wallet_balance }}</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Bank Details</label>
                            <textarea name="bank_details" class="form-control form-control-cosmic" rows="3"
                                placeholder="Account No, IFSC, Holder Name"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="submit" class="btn btn-cosmic w-100">Submit Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection