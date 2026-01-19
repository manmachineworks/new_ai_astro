@extends('admin.layouts.app')

@section('title', 'Astrologer Profile: ' . ($astrologer->astrologerProfile->display_name ?? $astrologer->name))
@section('page_title', 'Astrologer Detail')

@section('content')
    @php
        $profile = $profile ?? $astrologer->astrologerProfile;
        $status = $profile->verification_status ?? 'pending';
        $isVerified = $profile->is_verified || $status === 'approved' || $status === 'verified';
        $skills = is_array($profile->skills) ? implode(', ', $profile->skills) : ($profile->skills ?? '');
        $languages = is_array($profile->languages) ? implode(', ', $profile->languages) : ($profile->languages ?? '');
    @endphp

    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    <div class="avatar-circle mx-auto bg-primary text-white d-flex align-items-center justify-content-center mb-3"
                        style="width: 100px; height: 100px; border-radius: 50%; background-image: url('{{ $profile->profile_photo_path ? asset($profile->profile_photo_path) : '' }}'); background-size: cover;">
                        {{ !$profile->profile_photo_path ? strtoupper(substr($profile->display_name ?? $astrologer->name, 0, 1)) : '' }}
                    </div>

                    <h4>{{ $profile->display_name ?? $astrologer->name }}</h4>
                    <p class="text-muted mb-2">{{ $astrologer->email }}</p>

                    <span class="badge bg-{{ $isVerified ? 'success' : ($status === 'rejected' ? 'danger' : 'warning') }} mb-3">
                        {{ ucfirst($status) }}
                    </span>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Experience</span>
                        <span>{{ $profile->experience_years }} Years</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Wallet Balance</span>
                        <span class="fw-bold">{{ number_format($astrologer->wallet_balance, 2) }}</span>
                    </div>

                    <hr>

                    <div class="d-grid gap-2">
                        @can('verify_astrologers')
                            <form action="{{ route('admin.astrologers.toggleAccount', $astrologer->id) }}" method="POST"
                                data-confirm data-confirm-title="Toggle Account"
                                data-confirm-text="Toggle this astrologer's account status?">
                                @csrf
                                @method('PUT')
                                <button class="btn btn-outline-secondary">
                                    {{ $profile->is_enabled ? 'Disable Account' : 'Enable Account' }}
                                </button>
                            </form>
                        @endcan

                        @can('toggle_astrologer_visibility')
                            <form action="{{ route('admin.astrologers.toggleVisibility', $astrologer->id) }}" method="POST"
                                data-confirm data-confirm-title="Toggle Visibility"
                                data-confirm-text="Toggle visibility on the public directory?">
                                @csrf
                                @method('PUT')
                                <button class="btn btn-outline-primary">
                                    {{ $profile->show_on_front ? 'Hide from Frontend' : 'Show on Frontend' }}
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white fw-bold">Service Status</div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Calls</span>
                        <span class="badge bg-{{ $profile->is_call_enabled ? 'success' : 'secondary' }}">
                            {{ $profile->is_call_enabled ? 'Enabled' : 'Disabled' }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Chats</span>
                        <span class="badge bg-{{ $profile->is_chat_enabled ? 'success' : 'secondary' }}">
                            {{ $profile->is_chat_enabled ? 'Enabled' : 'Disabled' }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>SMS</span>
                        <span class="badge bg-{{ $profile->is_sms_enabled ? 'success' : 'secondary' }}">
                            {{ $profile->is_sms_enabled ? 'Enabled' : 'Disabled' }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Appointments</span>
                        <span class="badge bg-{{ $profile->is_appointment_enabled ? 'success' : 'secondary' }}">
                            {{ $profile->is_appointment_enabled ? 'Enabled' : 'Disabled' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <ul class="nav nav-tabs card-header-tabs" id="astroTab" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" id="overview-tab" data-bs-toggle="tab"
                                data-bs-target="#overview" type="button">Overview</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="verification-tab" data-bs-toggle="tab"
                                data-bs-target="#verification" type="button">Verification</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="services-tab" data-bs-toggle="tab" data-bs-target="#services"
                                type="button">Services & Pricing</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="schedule-tab" data-bs-toggle="tab" data-bs-target="#schedule"
                                type="button">Schedule</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="performance-tab" data-bs-toggle="tab"
                                data-bs-target="#performance" type="button">Performance</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews"
                                type="button">Reviews</button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="astroTabContent">
                        <!-- Overview Tab -->
                        <div class="tab-pane fade show active" id="overview" role="tabpanel">
                            @can('manage_astrologers')
                                <form action="{{ route('admin.astrologers.profile.update', $astrologer->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Display Name</label>
                                            <input type="text" name="display_name" class="form-control"
                                                value="{{ $profile->display_name }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Experience (Years)</label>
                                            <input type="number" name="experience_years" class="form-control"
                                                value="{{ $profile->experience_years }}" min="0">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Skills (comma-separated)</label>
                                        <input type="text" name="skills" class="form-control" value="{{ $skills }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Languages (comma-separated)</label>
                                        <input type="text" name="languages" class="form-control" value="{{ $languages }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Bio</label>
                                        <textarea name="bio" class="form-control" rows="4">{{ $profile->bio }}</textarea>
                                    </div>
                                    <div class="text-end">
                                        <button class="btn btn-primary">Save Profile</button>
                                    </div>
                                </form>
                            @else
                                <div class="text-muted">You do not have permission to edit profile details.</div>
                            @endcan
                        </div>

                        <!-- Verification Tab -->
                        <div class="tab-pane fade" id="verification" role="tabpanel">
                            <div class="alert alert-{{ $isVerified ? 'success' : ($status === 'rejected' ? 'danger' : 'warning') }}">
                                <strong>Status: {{ ucfirst($status) }}</strong>
                                @if($profile->rejection_reason)
                                    <div class="small">Reason: {{ $profile->rejection_reason }}</div>
                                @endif
                            </div>

                            @can('verify_astrologers')
                                <div class="d-flex gap-2 mb-3">
                                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">Approve</button>
                                    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">Reject</button>
                                </div>
                            @endcan

                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($profile->documents as $doc)
                                            <tr>
                                                <td>{{ ucfirst(str_replace('_', ' ', $doc->doc_type)) }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $doc->status == 'approved' ? 'success' : 'secondary' }}">
                                                        {{ ucfirst($doc->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="#" class="btn btn-sm btn-outline-primary"
                                                        onclick="alert('Document preview implementation pending storage setup')">View</a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">No documents uploaded.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Services & Pricing Tab -->
                        <div class="tab-pane fade" id="services" role="tabpanel">
                            @can('manage_astrologers')
                                <form action="{{ route('admin.astrologers.services.update', $astrologer->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Call Price (per minute)</label>
                                            <input type="number" step="0.01" name="call_per_minute" class="form-control"
                                                value="{{ $profile->call_per_minute }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Chat Price (per session)</label>
                                            <input type="number" step="0.01" name="chat_per_session" class="form-control"
                                                value="{{ $profile->chat_per_session }}">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="is_call_enabled" value="1"
                                                    id="callEnabled" @checked($profile->is_call_enabled)>
                                                <label class="form-check-label" for="callEnabled">Calls</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="is_chat_enabled" value="1"
                                                    id="chatEnabled" @checked($profile->is_chat_enabled)>
                                                <label class="form-check-label" for="chatEnabled">Chats</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="is_sms_enabled" value="1"
                                                    id="smsEnabled" @checked($profile->is_sms_enabled)>
                                                <label class="form-check-label" for="smsEnabled">SMS</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="is_appointment_enabled" value="1"
                                                    id="apptEnabled" @checked($profile->is_appointment_enabled)>
                                                <label class="form-check-label" for="apptEnabled">Appointments</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-end">
                                        <button class="btn btn-primary">Save Services</button>
                                    </div>
                                </form>
                            @else
                                <div class="text-muted">You do not have permission to update services.</div>
                            @endcan

                            <hr>

                            <h6 class="text-uppercase text-muted small">Pricing History</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Call / min</th>
                                            <th>Chat / session</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($pricingHistory as $entry)
                                            <tr>
                                                <td>{{ $entry->created_at->format('M d, Y') }}</td>
                                                <td>{{ number_format($entry->call_per_minute, 2) }}</td>
                                                <td>{{ number_format($entry->chat_per_session, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">No pricing history.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Schedule Tab -->
                        <div class="tab-pane fade" id="schedule" role="tabpanel">
                            <div class="row">
                                <div class="col-md-7">
                                    <h6 class="text-uppercase text-muted small">Weekly Slots</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Day</th>
                                                    <th>Start</th>
                                                    <th>End</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($profile->availabilityRules as $rule)
                                                    <tr>
                                                        <td>{{ ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'][$rule->day_of_week] }}</td>
                                                        <td>{{ $rule->start_time_utc }}</td>
                                                        <td>{{ $rule->end_time_utc }}</td>
                                                        <td class="text-end">
                                                            @can('manage_astrologers')
                                                                <form method="POST"
                                                                    action="{{ route('admin.astrologers.availability.rules.delete', [$astrologer->id, $rule->id]) }}"
                                                                    data-confirm data-confirm-title="Remove Slot"
                                                                    data-confirm-text="Remove this availability slot?">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button class="btn btn-sm btn-outline-danger">Remove</button>
                                                                </form>
                                                            @else
                                                                <span class="text-muted small">-</span>
                                                            @endcan
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center text-muted">No weekly slots configured.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <h6 class="text-uppercase text-muted small">Add Weekly Slot</h6>
                                    @can('manage_astrologers')
                                        <form method="POST" action="{{ route('admin.astrologers.availability.rules.store', $astrologer->id) }}">
                                            @csrf
                                            <div class="mb-2">
                                                <label class="form-label">Day of Week</label>
                                                <select name="day_of_week" class="form-select" required>
                                                    <option value="">Select Day</option>
                                                    @foreach(['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $idx => $day)
                                                        <option value="{{ $idx }}">{{ $day }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">Start (UTC)</label>
                                                <input type="time" name="start_time_utc" class="form-control" required>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">End (UTC)</label>
                                                <input type="time" name="end_time_utc" class="form-control" required>
                                            </div>
                                            <button class="btn btn-primary w-100">Add Slot</button>
                                        </form>
                                    @else
                                        <div class="text-muted small">No permission to edit availability.</div>
                                    @endcan
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-md-7">
                                    <h6 class="text-uppercase text-muted small">Exceptions / Leaves</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Type</th>
                                                    <th>Time</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($availabilityExceptions as $exception)
                                                    <tr>
                                                        <td>{{ $exception->date->format('M d, Y') }}</td>
                                                        <td><span class="badge bg-light text-dark border">{{ ucfirst($exception->type) }}</span></td>
                                                        <td>{{ $exception->start_time_utc ?? '-' }} - {{ $exception->end_time_utc ?? '-' }}</td>
                                                        <td class="text-end">
                                                            @can('manage_astrologers')
                                                                <form method="POST"
                                                                    action="{{ route('admin.astrologers.availability.exceptions.delete', [$astrologer->id, $exception->id]) }}"
                                                                    data-confirm data-confirm-title="Remove Exception"
                                                                    data-confirm-text="Remove this exception?">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button class="btn btn-sm btn-outline-danger">Remove</button>
                                                                </form>
                                                            @else
                                                                <span class="text-muted small">-</span>
                                                            @endcan
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center text-muted">No exceptions recorded.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <h6 class="text-uppercase text-muted small">Add Exception</h6>
                                    @can('manage_astrologers')
                                        <form method="POST" action="{{ route('admin.astrologers.availability.exceptions.store', $astrologer->id) }}">
                                            @csrf
                                            <div class="mb-2">
                                                <label class="form-label">Date</label>
                                                <input type="date" name="date" class="form-control" required>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">Type</label>
                                                <select name="type" class="form-select" required>
                                                    <option value="blocked">Blocked</option>
                                                    <option value="extra">Extra</option>
                                                </select>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">Start (UTC)</label>
                                                <input type="time" name="start_time_utc" class="form-control">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">End (UTC)</label>
                                                <input type="time" name="end_time_utc" class="form-control">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">Note</label>
                                                <input type="text" name="note" class="form-control" placeholder="Optional">
                                            </div>
                                            <button class="btn btn-primary w-100">Add Exception</button>
                                        </form>
                                    @else
                                        <div class="text-muted small">No permission to edit exceptions.</div>
                                    @endcan
                                </div>
                            </div>
                        </div>

                        <!-- Performance Tab -->
                        <div class="tab-pane fade" id="performance" role="tabpanel">
                            <div class="row g-3 mb-3">
                                <div class="col-md-3">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="text-muted small">Calls Completed</div>
                                            <div class="fw-bold">{{ $callStats->completed_calls ?? 0 }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="text-muted small">Calls Missed</div>
                                            <div class="fw-bold">{{ $callStats->missed_calls ?? 0 }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="text-muted small">Call Minutes</div>
                                            <div class="fw-bold">{{ number_format($callStats->total_minutes ?? 0, 2) }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="text-muted small">Call Revenue</div>
                                            <div class="fw-bold">{{ number_format($callStats->gross_revenue ?? 0, 2) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="text-muted small">Chat Sessions</div>
                                            <div class="fw-bold">{{ $chatStats->total_chats ?? 0 }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="text-muted small">Chat Messages</div>
                                            <div class="fw-bold">{{ $chatStats->total_messages ?? 0 }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="text-muted small">Total Earnings</div>
                                            <div class="fw-bold">{{ number_format($earningsSummary->total_earned ?? 0, 2) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3">
                                <div class="alert alert-light">
                                    Rating: {{ number_format($profile->rating_avg ?? 0, 2) }} / 5 ({{ $profile->rating_count ?? 0 }} ratings)
                                </div>
                            </div>
                        </div>

                        <!-- Reviews Tab -->
                        <div class="tab-pane fade" id="reviews" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="text-uppercase text-muted small">Latest Reviews</h6>
                                <a href="{{ route('admin.reviews.index', ['astrologer_id' => $profile->id]) }}" class="btn btn-sm btn-outline-secondary">View All</a>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>User</th>
                                            <th>Rating</th>
                                            <th>Comment</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($profile->reviews->take(10) as $review)
                                            <tr>
                                                <td>{{ $review->user?->name ?? 'User' }}</td>
                                                <td>{{ $review->rating }}</td>
                                                <td class="small text-muted">{{ $review->comment ?? '-' }}</td>
                                                <td><span class="badge bg-light text-dark border">{{ ucfirst($review->status) }}</span></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No reviews yet.</td>
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

    <!-- Approve Modal -->
    <div class="modal fade" id="approveModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('admin.astrologers.verify', $astrologer->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="approved">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Approve Astrologer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to approve <strong>{{ $profile->display_name }}</strong>?</p>
                        <p class="text-muted small">This will mark them as verified and allow them to take calls/chats if
                            enabled.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Approve</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('admin.astrologers.verify', $astrologer->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="rejected">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Reject Application</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                            <textarea name="rejection_reason" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Reject</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
