@extends('admin.layouts.app')

@section('title', 'Chat Details #' . Str::limit($chat->id, 8, ''))
@section('page_title', 'Chat Detail')

@section('content')
    <div class="row">
        <!-- Chat Overview -->
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Session Information</h5>
                    <span
                        class="badge bg-{{ $chat->status == 'completed' ? 'success' : ($chat->status == 'active' ? 'primary' : 'secondary') }} fs-6">{{ ucfirst($chat->status) }}</span>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6 border-end">
                            <label class="text-muted small text-uppercase">User</label>
                            @if($chat->user)
                                <div class="d-flex align-items-center mt-2">
                                    <div class="avatar-circle me-3 bg-secondary text-white d-flex align-items-center justify-content-center"
                                        style="width: 40px; height: 40px; border-radius: 50%;">
                                        {{ strtoupper(substr($chat->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold"><a href="{{ route('admin.users.show', $chat->user_id) }}"
                                                class="text-dark text-decoration-none">{{ $chat->user->name }}</a></div>
                                        <div class="small text-muted">{{ $chat->user->email }}</div>
                                    </div>
                                </div>
                            @else
                                <div class="text-danger mt-2">Unknown User (Deleted)</div>
                            @endif
                        </div>
                        <div class="col-md-6 ps-4">
                            <label class="text-muted small text-uppercase">Astrologer</label>
                            @if($chat->astrologer)
                                <div class="d-flex align-items-center mt-2">
                                    <div class="avatar-circle me-3 bg-primary text-white d-flex align-items-center justify-content-center"
                                        style="width: 40px; height: 40px; border-radius: 50%;">
                                        {{ strtoupper(substr($chat->astrologer->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold"><a
                                                href="{{ route('admin.astrologers.show', $chat->astrologer_user_id) }}"
                                                class="text-dark text-decoration-none">{{ $chat->astrologer->name }}</a></div>
                                        <div class="small text-muted">{{ $chat->astrologer->email }}</div>
                                    </div>
                                </div>
                            @else
                                <div class="text-danger mt-2">Unknown Astrologer</div>
                            @endif
                        </div>
                    </div>

                    <hr>

                    <!-- Timeline -->
                    <h6 class="text-muted text-uppercase mb-3 small">Timeline</h6>
                    <div class="timeline ps-4 border-start ms-2">
                        <div class="mb-3">
                            <span class="fw-bold">Started:</span>
                            {{ $chat->started_at ? $chat->started_at->format('M d, Y H:i:s') : '-' }}
                        </div>
                        <div class="mb-3">
                            <span class="fw-bold">Ended:</span>
                            {{ $chat->ended_at ? $chat->ended_at->format('M d, Y H:i:s') : '-' }}
                        </div>
                        <div>
                            <span class="fw-bold">Duration:</span> {{ $chat->duration_minutes }} mins
                        </div>
                    </div>

                    @if($chat->firebase_chat_id)
                        <div class="mt-4">
                            <div class="alert alert-info small">
                                <i class="fas fa-database me-2"></i> Firebase Reference:
                                <code>{{ $chat->firebase_chat_id }}</code>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Reports & Flags</h5>
                    <span class="badge bg-light text-dark border">{{ $chat->reports->count() }} Reports</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Reported By</th>
                                    <th>Reason</th>
                                    <th>Details</th>
                                    <th>When</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($chat->reports as $report)
                                    <tr>
                                        <td>{{ $report->reporter?->name ?? 'User' }}</td>
                                        <td><span class="badge bg-warning-subtle text-warning">{{ $report->reason }}</span></td>
                                        <td class="small text-muted">{{ $report->details ?? '-' }}</td>
                                        <td class="small text-muted">{{ $report->created_at->format('M d, H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">No reports submitted.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Billing Sidebar -->
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white fw-bold">Billing Details</div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Rate</span>
                        <span class="fw-bold">{{ $chat->rate_per_minute }}/min</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Billable Minutes</span>
                        <span class="fw-bold">{{ $chat->duration_minutes }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-bold">Total Cost</span>
                        <span class="fw-bold text-success fs-5">{{ $chat->cost }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Platform Fee</span>
                        <span class="text-danger small">-{{ number_format($chat->commission_amount_total, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Commission %</span>
                        <span class="small">{{ $chat->commission_percent_snapshot }}%</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="fw-bold text-secondary">Astrologer Earning</span>
                        <span
                            class="fw-bold text-secondary">{{ number_format($chat->cost - $chat->commission_amount_total, 2) }}</span>
                    </div>

                    <div class="alert alert-light border small mb-0">
                        <strong>Last Billed:</strong>
                        {{ $chat->last_billed_at ? $chat->last_billed_at->format('Y-m-d H:i') : 'Pending' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
