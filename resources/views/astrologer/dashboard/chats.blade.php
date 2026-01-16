@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row">
            <div class="col-md-3">
                @include('astrologer.dashboard.nav')
            </div>
            <div class="col-md-9">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Active Chats</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        @forelse($sessions as $session)
                            <a href="{{ route('user.chats.show', $session->conversation_id) }}"
                                class="list-group-item list-group-item-action py-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                        style="width: 48px; height: 48px;">
                                        {{ substr($session->user->name ?: 'U', 0, 1) }}
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="mb-0 fw-bold">
                                                {{ $session->user_masked_identifier ?: 'User #' . substr($session->user->id, -4) }}
                                            </h6>
                                            <small class="text-muted">{{ $session->updated_at->diffForHumans() }}</small>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <p class="mb-0 text-muted small text-truncate" style="max-width: 300px;">
                                                Start chatting with client...
                                            </p>
                                            @if($session->total_messages_user > 0)
                                                <span
                                                    class="badge bg-primary rounded-pill small">{{ $session->total_messages_user + $session->total_messages_astrologer }}
                                                    msgs</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="text-center py-5 text-muted">
                                No active chats. Make sure your chat status is enabled.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection