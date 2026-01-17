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

                        <form action="{{ route('admin.users.toggle', $user->id) }}" method="POST" class="d-block"
                            onsubmit="return confirm('Confirm action?')">
                            @csrf
                            <button
                                class="btn btn-outline-danger w-100">{{ $user->is_active ? 'Block User' : 'Unblock User' }}</button>
                        </form>
                    </div>
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
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="myTabContent">
                        <!-- Wallet Tab -->
                        <div class="tab-pane fade show active" id="wallet" role="tabpanel">
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
                                                <td>-</td>
                                                <td>{{ $chat->amount_charged ?? 0 }}</td>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection