@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.calls.index') }}">Call Logs</a></li>
                <li class="breadcrumb-item active">Call Audit #{{ substr($call->id, 0, 8) }}</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 text-primary">Call Lifecycle Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-6 col-md-3 mb-3">
                                <label class="text-muted small d-block">Status</label>
                                <span class="badge bg-{{ $call->status === 'completed' ? 'success' : 'warning' }} fs-6">
                                    {{ ucfirst($call->status) }}
                                </span>
                            </div>
                            <div class="col-6 col-md-3 mb-3">
                                <label class="text-muted small d-block">Provider ID</label>
                                <span class="fw-bold">{{ $call->provider_call_id ?: 'N/A' }}</span>
                            </div>
                            <div class="col-6 col-md-3 mb-3">
                                <label class="text-muted small d-block">Duration</label>
                                <span class="fw-bold">{{ floor($call->duration_seconds / 60) }}m
                                    {{ $call->duration_seconds % 60 }}s</span>
                            </div>
                            <div class="col-6 col-md-3 mb-3">
                                <label class="text-muted small d-block">Billable Minutes</label>
                                <span class="fw-bold text-primary">{{ $call->billable_minutes }}</span>
                            </div>
                        </div>

                        <h6 class="border-bottom pb-2 mb-3">Timestamps (UTC)</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <small class="text-muted d-block">Ringing Start</small>
                                {{ $call->started_at_utc ? $call->started_at_utc->format('M d, H:i:s') : '-' }}
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Connected</small>
                                {{ $call->connected_at_utc ? $call->connected_at_utc->format('M d, H:i:s') : '-' }}
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Ended</small>
                                {{ $call->ended_at_utc ? $call->ended_at_utc->format('M d, H:i:s') : '-' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Webhook Timeline</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @forelse($webhooks as $event)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-1 text-primary">{{ strtoupper($event->event_type) }}</h6>
                                        <small class="text-muted">{{ $event->created_at->format('H:i:s.v') }}</small>
                                    </div>
                                    <pre class="bg-light p-2 rounded small mt-2 mb-0"
                                        style="max-height: 150px; overflow-y: auto;">{{ json_encode($event->payload, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            @empty
                                <div class="p-4 text-center text-muted">No webhooks recorded for this call ID.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-0 mb-4 bg-primary text-white">
                    <div class="card-body">
                        <h6 class="card-title opacity-75">Financial Breakdown</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Gross User Charge:</span>
                            <span class="fw-bold">₹{{ number_format($call->gross_amount, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Platform
                                ({{ number_format($call->gross_amount > 0 ? ($call->platform_commission_amount / $call->gross_amount) * 100 : 0) }}%):</span>
                            <span class="fw-bold">₹{{ number_format($call->platform_commission_amount, 2) }}</span>
                        </div>
                        <hr class="my-2 border-white opacity-25">
                        <div class="d-flex justify-content-between">
                            <span class="h6 mb-0">Astrologer Earnings:</span>
                            <span class="h6 mb-0 fw-bold">₹{{ number_format($call->astrologer_earnings_amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Participants</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="text-muted small d-block">User (Full Info)</label>
                            <div class="fw-bold">{{ $call->user->name }}</div>
                            <div>{{ $call->user->phone }}</div>
                        </div>
                        <div>
                            <label class="text-muted small d-block">Astrologer (Full Info)</label>
                            <div class="fw-bold">{{ $call->astrologerProfile->display_name }}</div>
                            <div>{{ $call->astrologerProfile->user->phone }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection