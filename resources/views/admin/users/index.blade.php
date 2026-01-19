@extends('admin.layouts.app')

@section('title', 'User Management')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold m-0 text-dark">User Management</h2>
        <a href="{{ route('admin.users.index', array_merge(request()->query(), ['export' => 'csv'])) }}"
            class="btn btn-outline-secondary rounded-pill px-4">
            <i class="fas fa-file-csv me-2"></i>Export CSV
        </a>
    </div>

    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                        placeholder="Name, email, phone">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="active" @selected(request('status') === 'active')>Active</option>
                        <option value="blocked" @selected(request('status') === 'blocked')>Blocked</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Role</label>
                    <select name="role" class="form-select">
                        <option value="">All</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" @selected(request('role') === $role->name)>{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Joined From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Joined To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Wallet Min</label>
                    <input type="number" step="0.01" min="0" name="wallet_min" value="{{ request('wallet_min') }}"
                        class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Wallet Max</label>
                    <input type="number" step="0.01" min="0" name="wallet_max" value="{{ request('wallet_max') }}"
                        class="form-control">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-light w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <form action="{{ route('admin.users.bulk_action') }}" method="POST" id="bulkActionForm">
        @csrf
        <div class="card shadow-sm border-0 rounded-4 mb-3" id="bulkActionsCard" style="display:none;">
            <div class="card-body py-2 d-flex align-items-center justify-content-between bg-light rounded-4">
                <div class="d-flex align-items-center">
                    <span class="fw-bold me-3 text-primary"><span id="selectedCount">0</span> Selected</span>
                    <select name="action" class="form-select form-select-sm border-0 bg-white shadow-sm"
                        style="width: 200px;" required>
                        <option value="">Choose Action...</option>
                        <option value="activate">Bulk Activate</option>
                        <option value="deactivate">Bulk Deactivate</option>
                        <option value="export">Export Selected</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-dark btn-sm rounded-pill px-4"
                    onclick="return confirm('Are you sure you want to perform this bulk action?');">Apply</button>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4" style="width: 50px;">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                </div>
                            </th>
                            <th>User</th>
                            <th>Wallet</th>
                        <th>Status</th>
                        <th>Registered</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td class="ps-4">
                                    <div class="form-check">
                                        <input class="form-check-input user-checkbox" type="checkbox" name="ids[]"
                                            value="{{ $user->id }}">
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle bg-primary text-white d-flex align-items-center justify-content-center me-3"
                                            style="width: 40px; height: 40px; font-size: 1rem;">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $user->name }}</div>
                                            <div class="small text-muted">{{ $user->email }}</div>
                                            <div class="small text-muted">{{ $user->phone }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">₹{{ number_format($user->wallet_balance, 2) }}</div>
                                </td>
                                <td>
                                    @if($user->is_active)
                                        <span class="badge bg-success-subtle text-success rounded-pill px-3">Active</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger rounded-pill px-3">Banned</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="small text-muted">{{ $user->created_at->format('M d, Y') }}</span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light rounded-circle" type="button"
                                            data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v text-muted"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow rounded-4">
                                            <li><a class="dropdown-item" href="{{ route('admin.users.edit', $user) }}"><i
                                                        class="fas fa-edit me-2 text-primary"></i> Edit</a></li>
                                            <li><a class="dropdown-item" href="{{ route('admin.wallets.show', $user->id) }}"><i
                                                        class="fas fa-wallet me-2 text-warning"></i> Wallet</a></li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <!-- Standard Single Toggle -->
                                                {{-- Use JS to submit or separate form --}}
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted mb-2"><i class="fas fa-users-slash fa-3x opacity-25"></i></div>
                                    <p class="text-muted">No users found matching your filters.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($users->hasPages())
                <div class="card-footer bg-white border-0 py-3">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </form>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const selectAll = document.getElementById('selectAll');
                const checkboxes = document.querySelectorAll('.user-checkbox');
                const bulkActionsCard = document.getElementById('bulkActionsCard');
                const selectedCountSpan = document.getElementById('selectedCount');

                function updateBulkUI() {
                    const checkedCount = document.querySelectorAll('.user-checkbox:checked').length;
                    selectedCountSpan.textContent = checkedCount;
                    if (checkedCount > 0) {
                        bulkActionsCard.style.display = 'block';
                    } else {
                        bulkActionsCard.style.display = 'none';
                    }
                }

                selectAll.addEventListener('change', function () {
                    checkboxes.forEach(cb => cb.checked = this.checked);
                    updateBulkUI();
                });

                checkboxes.forEach(cb => {
                    cb.addEventListener('change', updateBulkUI);
                });
            });
        </script>
    @endpush
@endsection
