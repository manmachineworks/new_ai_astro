@extends('admin.layouts.app')

@section('title', 'Call Details #' . Str::limit($call->id, 8, ''))
@section('page_title', 'Call Detail')

@section('content')
    <div class="row">
        <!-- Call Overview -->
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Session Information</h5>
                    <span
                        class="badge bg-{{ $call->status == 'completed' ? 'success' : 'secondary' }} fs-6">{{ ucfirst($call->status) }}</span>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6 border-end">
                            <label class="text-muted small text-uppercase">User</label>
                            @if($call->user)
                                <div class="d-flex align-items-center mt-2">
                                    <div class="avatar-circle me-3 bg-secondary text-white d-flex align-items-center justify-content-center"
                                        style="width: 40px; height: 40px; border-radius: 50%;">
                                        {{ strtoupper(substr($call->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold"><a href="{{ route('admin.users.show', $call->user_id) }}"
                                                class="text-dark text-decoration-none">{{ $call->user->name }}</a></div>
                                        <div class="small text-muted">{{ $call->user->email }}</div>
                                        <div class="small text-muted">ID: {{ $call->user_id }}</div>
                                    </div>
                                </div>
                            @else
                                <div class="text-danger mt-2">Unknown User (Deleted)</div>
                            @endif
                        </div>
                        <div class="col-md-6 ps-4">
                            <label class="text-muted small text-uppercase">Astrologer</label>
                            @if($call->astrologerProfile)
                                <div class="d-flex align-items-center mt-2">
                                    <div class="avatar-circle me-3 bg-primary text-white d-flex align-items-center justify-content-center"
                                        style="width: 40px; height: 40px; border-radius: 50%; background-image: url('{{ $call->astrologerProfile->profile_photo_path ? asset($call->astrologerProfile->profile_photo_path) : '' }}'); background-size: cover;">
                                        {{ !$call->astrologerProfile->profile_photo_path ? 'A' : '' }}
                                    </div>
                                    <div>
                                        <div class="fw-bold"><a
                                                href="{{ route('admin.astrologers.show', $call->astrologerProfile->user_id) }}"
                                                class="text-dark text-decoration-none">{{ $call->astrologerProfile->display_name }}</a>
                                        </div>
                                        <div class="small text-muted">{{ $call->astrologerProfile->user->email ?? 'no-email' }}
                                        </div>
                                        <div class="small text-muted">ID: {{ $call->astrologerProfile->user_id }}</div>
                                    </div>
                                </div>
                            @else
                                <div class="text-danger mt-2">Unknown Astrologer</div>
                            @endif
                        </div>
                    </div>

                    <hr>

                    <!-- Timeline -->
                    <h6 class="text-muted text-uppercase mb-3 small">Timeline (UTC)</h6>
                    <div class="timeline position-relative ps-4 border-start ms-2">
                        <div class="mb-3 position-relative">
                            <i class="fas fa-play d-block position-absolute bg-white text-muted"
                                style="left: -21px; font-size: 12px;"></i>
                            <span class="fw-bold">Started:</span>
                            {{ $call->started_at_utc ? $call->started_at_utc->format('Y-m-d H:i:s') : '-' }}
                        </div>
                        <div class="mb-3 position-relative">
                            <i class="fas fa-link d-block position-absolute bg-white text-info"
                                style="left: -21px; font-size: 12px;"></i>
                            <span class="fw-bold">Connected:</span>
                            {{ $call->connected_at_utc ? $call->connected_at_utc->format('Y-m-d H:i:s') : '-' }}
                        </div>
                        <div class="mb-3 position-relative">
                            <i class="fas fa-stop d-block position-absolute bg-white text-danger"
                                style="left: -21px; font-size: 12px;"></i>
                            <span class="fw-bold">Ended:</span>
                            {{ $call->ended_at_utc ? $call->ended_at_utc->format('Y-m-d H:i:s') : '-' }}
                        </div>
                        <div>
                            <span class="fw-bold">Total Duration:</span> {{ gmdate('H:i:s', $call->duration_seconds) }}
                        </div>
                    </div>

                    @php $recordingUrl = data_get($call->meta_json, 'recording_url'); @endphp
                    @if(!empty($recordingUrl))
                        <div class="mt-3">
                            <div class="alert alert-secondary small mb-0">
                                <i class="fas fa-music me-2"></i> Recording:
                                <a href="{{ $recordingUrl }}" target="_blank">Open recording</a>
                            </div>
                        </div>
                    @endif

                    @if($call->meta_json)
                        <hr>
                        <h6 class="text-muted text-uppercase mb-2 small">Metadata</h6>
                        <pre
                            class="bg-light p-3 border rounded small text-muted">{{ json_encode($call->meta_json, JSON_PRETTY_PRINT) }}</pre>
                    @endif
                </div>
            </div>

            @if(isset($webhooks) && $webhooks->count() > 0)
                <!-- Webhook Audit Log -->
                <div class="card shadow-sm">
                    <div class="card-header bg-light">Webhook Events (Debug)</div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-striped mb-0 small">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Event</th>
                                    <th>Payload Snippet</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($webhooks as $hook)
                                    <tr>
                                        <td>{{ $hook->created_at->format('H:i:s') }}</td>
                                        <td>{{ $hook->event_type }}</td>
                                        <td>{{ Str::limit(json_encode($hook->payload), 50) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <!-- Billing Sidebar -->
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white fw-bold">Billing Details</div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Rate</span>
                        <span class="fw-bold">{{ $call->rate_per_minute }}/min</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Billable Time</span>
                        <span class="fw-bold">{{ $call->billable_minutes }} mins</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-bold">Gross Amount</span>
                        <span class="fw-bold text-success fs-5">{{ $call->gross_amount }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Platform Fee</span>
                        <span class="text-danger small">-{{ $call->platform_commission_amount }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Commission %</span>
                        <span class="small">{{ $call->commission_percent_snapshot ?? '0' }}%</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="fw-bold text-secondary">Astrologer Earning</span>
                        <span class="fw-bold text-secondary">{{ $call->astrologer_earnings_amount }}</span>
                    </div>

                    <div class="alert alert-light border small mb-0">
                        <strong>Wallet Hold ID:</strong> {{ $call->wallet_hold_id ?? 'N/A' }}<br>
                        <strong>Settled At:</strong>
                        {{ $call->settled_at ? $call->settled_at->format('Y-m-d H:i') : 'Pending' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
