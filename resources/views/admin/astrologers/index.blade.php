@extends('admin.layouts.app')

@section('title', 'Astrologer Management')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold m-0 text-dark">Astrologers</h2>
        <!-- Stats or other top-right items -->
    </div>

    <x-admin.filter-bar :action="route('admin.astrologers.index')" :filters="['search', 'status', 'export']" />

    <form action="{{ route('admin.astrologers.bulk_action') }}" method="POST" id="bulkActionForm">
        @csrf
        <div class="card shadow-sm border-0 rounded-4 mb-3" id="bulkActionsCard" style="display:none;">
            <div class="card-body py-2 d-flex align-items-center justify-content-between bg-light rounded-4">
                <div class="d-flex align-items-center">
                    <span class="fw-bold me-3 text-primary"><span id="selectedCount">0</span> Selected</span>
                    <select name="action" class="form-select form-select-sm border-0 bg-white shadow-sm"
                        style="width: 220px;" required>
                        <option value="">Choose Action...</option>
                        <option value="verify_approve">Mark Verified</option>
                        <option value="verify_reject">Mark Rejected</option>
                        <option value="enable_front">Enable Account</option>
                        <option value="disable_front">Disable Account</option>
                        <option value="export">Export Selected</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-dark btn-sm rounded-pill px-4"
                    onclick="return confirm('Are you sure you want to perform this bulk action?');">Apply</button>
            </div>
        </div>

        <x-admin.table :columns="['', 'Astrologer', 'Status', 'Integration', 'Orders', 'Actions']" :rows="$astrologers">
            <x-slot name="header_extra">
                <th class="ps-4" style="width: 50px;">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="selectAll">
                    </div>
                </th>
            </x-slot>

            @forelse($astrologers as $astrologer)
                <tr>
                    <td class="ps-4">
                        <div class="form-check">
                            <input class="form-check-input user-checkbox" type="checkbox" name="ids[]"
                                value="{{ $astrologer->id }}">
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle-sm bg-indigo-100 text-indigo me-3 rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 40px; height: 40px; font-weight: 600;">
                                {{ substr($astrologer->astrologerProfile->display_name ?? $astrologer->name, 0, 1) }}
                            </div>
                            <div>
                                <div class="fw-bold text-dark">
                                    {{ $astrologer->astrologerProfile->display_name ?? $astrologer->name }}</div>
                                <div class="small text-muted">{{ $astrologer->phone }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <!-- Verification Status -->
                        @if(($astrologer->astrologerProfile->verification_status ?? '') === 'verified')
                            <span class="badge bg-success-subtle text-success rounded-pill px-2" data-bs-toggle="tooltip"
                                title="Verified"><i class="fas fa-check-circle"></i></span>
                        @elseif(($astrologer->astrologerProfile->verification_status ?? '') === 'rejected')
                            <span class="badge bg-danger-subtle text-danger rounded-pill px-2" data-bs-toggle="tooltip"
                                title="Rejected"><i class="fas fa-times-circle"></i></span>
                        @else
                            <span class="badge bg-warning-subtle text-warning rounded-pill px-2" data-bs-toggle="tooltip"
                                title="Pending"><i class="fas fa-clock"></i></span>
                        @endif

                        <!-- Active Status -->
                        @if($astrologer->is_active)
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-2 ms-1" data-bs-toggle="tooltip"
                                title="Active on Platform">ON</span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 ms-1" data-bs-toggle="tooltip"
                                title="Hidden/Disabled">OFF</span>
                        @endif
                    </td>
                    <td>
                        <div class="small text-muted">
                            @if($astrologer->astrologerProfile && $astrologer->astrologerProfile->chat_enabled) <i
                            class="fas fa-comments text-success me-1" title="Chat On"></i> @endif
                            @if($astrologer->astrologerProfile && $astrologer->astrologerProfile->call_enabled) <i
                            class="fas fa-phone text-success me-1" title="Call On"></i> @endif
                            @if($astrologer->astrologerProfile && $astrologer->astrologerProfile->ai_enabled) <i
                            class="fas fa-robot text-primary" title="AI On"></i> @endif
                        </div>
                    </td>
                    <td>
                        <div class="small fw-bold">{{ $astrologer->calls_count ?? 0 }} calls</div>
                        <div class="small text-muted">{{ $astrologer->chats_count ?? 0 }} chats</div>
                    </td>
                    <td class="text-end pe-4">
                        <a href="{{ route('admin.astrologers.show', $astrologer->id) }}"
                            class="btn btn-sm btn-light rounded-circle text-primary" data-bs-toggle="tooltip"
                            title="View Profile">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <p class="text-muted">No astrologers found.</p>
                    </td>
                </tr>
            @endforelse
        </x-admin.table>
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