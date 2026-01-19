@extends('admin.layouts.app')

@section('title', 'User Profile: ' . $user->name)
@section('page_title', 'User Profile')

@section('content')
    <div class="row">
        <!-- Sidebar / Overview -->
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    <div class="avatar-circle mx-auto bg-primary text-white d-flex align-items-center justify-content-center mb-3"
                        style="width: 80px; height: 80px; border-radius: 50%; font-size: 2rem;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <h4>{{ $user->name }}</h4>
                    <p class="text-muted">{{ $user->email }}</p>
                    @foreach($user->roles as $role)
                        <span class="badge bg-info text-dark mb-2">{{ $role->name }}</span>
                    @endforeach

                    <hr>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Status</span>
                        <span
                            class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">{{ $user->is_active ? 'Active' : 'Banned' }}</span>
                    </div>
                    @if(!$user->is_active)
                        <div class="alert alert-warning small mb-3">
                            <div class="fw-bold">Blocked</div>
                            <div>Until: {{ $user->blocked_until ? $user->blocked_until->format('M d, Y H:i') : 'Indefinite' }}</div>
                            @if($user->blocked_reason)
                                <div class="mt-1">Reason: {{ $user->blocked_reason }}</div>
                            @endif
                        </div>
                    @endif
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Joined</span>
                        <span>{{ $user->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Phone</span>
                        <span>{{ $user->phone ?? 'N/A' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Wallet Balance</span>
                        <span
                            class="fw-bold {{ $user->wallet_balance < 0 ? 'text-danger' : 'text-success' }}">{{ number_format($user->wallet_balance, 2) }}</span>
                    </div>

                    <hr>
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-outline-primary">Edit
                            Profile</a>

                        @if($user->is_active)
                            <button class="btn btn-outline-danger w-100" data-bs-toggle="modal"
                                data-bs-target="#blockUserModal">Block User</button>
                        @else
                            <form action="{{ route('admin.users.unblock', $user->id) }}" method="POST" class="d-block"
                                data-confirm data-confirm-title="Unblock User"
                                data-confirm-text="Unblock this user and restore access?">
                                @csrf
                                <button class="btn btn-outline-success w-100">Unblock User</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white fw-bold">AI Chat Restrictions</div>
                <div class="card-body">
                    @if($aiRestriction)
                        <div class="alert alert-warning small">
                            <div class="fw-bold">AI Chat Blocked</div>
                            <div>Until: {{ $aiRestriction->expires_at ? $aiRestriction->expires_at->format('M d, Y H:i') : 'Indefinite' }}</div>
                            @if(!empty($aiRestriction->meta_json['reason']))
                                <div class="mt-1">Reason: {{ $aiRestriction->meta_json['reason'] }}</div>
                            @endif
                        </div>
                        <form action="{{ route('admin.users.ai_chat.unblock', $user->id) }}" method="POST"
                            data-confirm data-confirm-title="Lift AI Chat Restriction"
                            data-confirm-text="Allow AI chat access for this user?">
                            @csrf
                            <button class="btn btn-outline-success w-100">Lift Restriction</button>
                        </form>
                    @else
                        <button class="btn btn-outline-warning w-100" data-bs-toggle="modal"
                            data-bs-target="#aiBlockModal">Restrict AI Chat</button>
                    @endif
                </div>
            </div>

            @if($user->astrologerProfile)
                <div class="card shadow-sm mb-4 border-info">
                    <div class="card-header bg-info text-dark fw-bold">Astrologer Profile</div>
                    <div class="card-body">
                        <p class="mb-1"><strong>Specialties:</strong> {{ $user->astrologerProfile->specialties ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>Experience:</strong> {{ $user->astrologerProfile->experience_years }} years</p>
                        <p class="mb-0"><strong>Verified:</strong> {{ $user->astrologerProfile->is_verified ? 'Yes' : 'No' }}
                        </p>
                        <div class="mt-3 text-end">
                            <a href="{{ route('admin.astrologers.show', $user->astrologerProfile->id) }}"
                                class="btn btn-sm btn-info">View Astrologer Detail</a>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Main Content -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" id="wallet-tab" data-bs-toggle="tab" data-bs-target="#wallet"
                                type="button">Wallet Ledger</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="calls-tab" data-bs-toggle="tab" data-bs-target="#calls"
                                type="button">Call History</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="chats-tab" data-bs-toggle="tab" data-bs-target="#chats"
                                type="button">Chat History</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="ai-tab" data-bs-toggle="tab" data-bs-target="#ai"
                                type="button">AI Chat</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="appointments-tab" data-bs-toggle="tab"
                                data-bs-target="#appointments" type="button">Appointments</button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="myTabContent">
                        <!-- Wallet Tab -->
                        <div class="tab-pane fade show active" id="wallet" role="tabpanel">
                            <div class="d-flex justify-content-end mb-2">
                                <a href="{{ route('admin.finance.wallets.show', $user->id) }}" class="btn btn-sm btn-outline-secondary">
                                    View Full Ledger
                                </a>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Type</th>
                                            <th>Amount</th>
                                            <th>Balance After</th>
                                            <th>Desc</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($walletTransactions as $txn)
                                            <tr>
                                                <td>{{ $txn->created_at->format('M d H:i') }}</td>
                                                <td>{{ $txn->type }}</td>
                                                <td class="{{ $txn->amount > 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ $txn->amount > 0 ? '+' : '' }}{{ $txn->amount }}
                                                </td>
                                                <td>{{ $txn->balance_after ?? '-' }}</td>
                                                <td class="small text-muted">{{ $txn->description }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">No transactions found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Calls Tab -->
                        <div class="tab-pane fade" id="calls" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Duration</th>
                                            <th>Cost</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($callSessions as $call)
                                            <tr>
                                                <td>{{ $call->created_at->format('M d H:i') }}</td>
                                                <td>{{ gmdate('H:i:s', $call->duration_seconds) }}</td>
                                                <td>{{ $call->amount_charged }}</td>
                                                <td><span class="badge bg-secondary">{{ $call->status }}</span></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No calls found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Chats Tab -->
                        <div class="tab-pane fade" id="chats" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Messages</th>
                                            <th>Cost</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($chatSessions as $chat)
                                            <tr>
                                                <td>{{ $chat->created_at->format('M d H:i') }}</td>
                                                <td>{{ ($chat->total_messages_user ?? 0) + ($chat->total_messages_astrologer ?? 0) }}</td>
                                                <td>{{ $chat->total_charged ?? 0 }}</td>
                                                <td><span class="badge bg-secondary">{{ $chat->status ?? 'N/A' }}</span></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No chats found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- AI Chats Tab -->
                        <div class="tab-pane fade" id="ai" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Messages</th>
                                            <th>Charged</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($aiSessions as $session)
                                            <tr>
                                                <td>{{ $session->created_at->format('M d H:i') }}</td>
                                                <td>{{ $session->total_messages ?? 0 }}</td>
                                                <td>{{ $session->total_charged ?? 0 }}</td>
                                                <td><span class="badge bg-secondary">{{ $session->status }}</span></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No AI sessions found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Appointments Tab -->
                        <div class="tab-pane fade" id="appointments" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Astrologer</th>
                                            <th>Status</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($appointments as $appointment)
                                            <tr>
                                                <td>{{ $appointment->created_at->format('M d H:i') }}</td>
                                                <td>{{ $appointment->astrologerProfile?->display_name ?? 'Astrologer' }}</td>
                                                <td><span class="badge bg-secondary">{{ $appointment->status }}</span></td>
                                                <td>{{ $appointment->price_total ?? 0 }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No appointments found</td>
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

    <!-- Block User Modal -->
    <div class="modal fade" id="blockUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('admin.users.block', $user->id) }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Block User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <textarea name="reason" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Duration (minutes)</label>
                        <input type="number" name="duration_minutes" class="form-control" min="1" placeholder="e.g., 1440">
                        <div class="form-text">Leave empty to use a specific blocked-until date.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Blocked Until</label>
                        <input type="datetime-local" name="blocked_until" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Block User</button>
                </div>
            </form>
        </div>
    </div>

    <!-- AI Chat Restriction Modal -->
    <div class="modal fade" id="aiBlockModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('admin.users.ai_chat.block', $user->id) }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Restrict AI Chat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Reason (optional)</label>
                        <textarea name="reason" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Duration (minutes)</label>
                        <input type="number" name="duration_minutes" class="form-control" min="1"
                            placeholder="e.g., 1440">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Apply Restriction</button>
                </div>
            </form>
        </div>
    </div>
@endsection
