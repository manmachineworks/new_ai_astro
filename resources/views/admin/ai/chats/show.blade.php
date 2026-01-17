@extends('admin.layouts.app')

@section('title', 'AI Session Details #' . Str::limit($session->id, 8, ''))
@section('page_title', 'AI Session Detail')

@section('content')
    <div class="row">
        <!-- Chat Transcript -->
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Transcript</h5>
                    <span
                        class="badge bg-{{ $session->status == 'completed' ? 'secondary' : 'primary' }}">{{ ucfirst($session->status) }}</span>
                </div>
                <div class="card-body bg-light" style="max-height: 600px; overflow-y: auto;">
                    @forelse($session->messages as $msg)
                        <div class="d-flex mb-3 {{ $msg->is_ai ? 'justify-content-start' : 'justify-content-end' }}">
                            <div class="d-flex {{ $msg->is_ai ? 'flex-row' : 'flex-row-reverse' }}" style="max-width: 80%;">
                                <!-- Avatar -->
                                <div class="flex-shrink-0 {{ $msg->is_ai ? 'me-2' : 'ms-2' }}">
                                    @if($msg->is_ai)
                                        <div class="avatar-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                            style="width: 35px; height: 35px; border-radius: 50%;">
                                            <i class="fas fa-robot"></i>
                                        </div>
                                    @else
                                        <div class="avatar-circle bg-secondary text-white d-flex align-items-center justify-content-center"
                                            style="width: 35px; height: 35px; border-radius: 50%;">
                                            {{ strtoupper(substr($session->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                    @endif
                                </div>

                                <!-- Bubble -->
                                <div class="card shadow-sm border-0 {{ $msg->is_ai ? '' : 'bg-primary text-white' }}">
                                    <div class="card-body p-2">
                                        <p class="mb-1 small">{{ $msg->message_content }}</p>
                                        <div class="text-end" style="font-size: 0.7rem; opacity: 0.7;">
                                            {{ $msg->created_at->format('H:i:s') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted my-5">
                            <i class="fas fa-comment-slash fa-2x mb-2"></i>
                            <p>No messages recorded for this session.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Info Sidebar -->
        <div class="col-md-4">
            <!-- User Info -->
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-white fw-bold">User Information</div>
                <div class="card-body">
                    @if($session->user)
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle me-3 bg-secondary text-white d-flex align-items-center justify-content-center"
                                style="width: 40px; height: 40px; border-radius: 50%;">
                                {{ strtoupper(substr($session->user->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-bold"><a href="{{ route('admin.users.show', $session->user_id) }}"
                                        class="text-dark text-decoration-none">{{ $session->user->name }}</a></div>
                                <div class="small text-muted">{{ $session->user->email }}</div>
                            </div>
                        </div>
                    @else
                        <div class="text-danger">Unknown User (Deleted)</div>
                    @endif
                </div>
            </div>

            <!-- Billing Info -->
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-success text-white fw-bold">Billing Details</div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Mode</span>
                        <span class="fw-bold">{{ ucfirst(str_replace('_', ' ', $session->pricing_mode)) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Unit Price</span>
                        <span class="fw-bold">
                            @if($session->pricing_mode == 'per_message')
                                {{ $session->price_per_message }}/msg
                            @else
                                {{ $session->session_price }}/session
                            @endif
                        </span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Messages</span>
                        <span class="fw-bold">{{ $session->total_messages }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-bold">Total Charged</span>
                        <span class="fw-bold text-success fs-5">{{ number_format($session->total_charged, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Timeline -->
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-bold">Timeline</div>
                <div class="card-body">
                    <div class="timeline ps-3 border-start ms-2">
                        <div class="mb-3">
                            <div class="fw-bold small">Started</div>
                            <div class="text-muted small">
                                {{ $session->started_at ? $session->started_at->format('M d, Y H:i:s') : '-' }}</div>
                        </div>
                        <div>
                            <div class="fw-bold small">Ended</div>
                            <div class="text-muted small">
                                {{ $session->ended_at ? $session->ended_at->format('M d, Y H:i:s') : 'In Progress' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection