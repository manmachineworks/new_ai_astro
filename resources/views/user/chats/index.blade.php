@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h4 class="mb-4">My Chats</h4>

                <div class="card shadow-sm border-0 overflow-hidden">
                    <div class="list-group list-group-flush">
                        @forelse($sessions as $session)
                            <a href="{{ route('user.chats.show', $session->conversation_id) }}"
                                class="list-group-item list-group-item-action py-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                        style="width: 48px; height: 48px;">
                                        {{ substr($session->astrologerProfile->display_name, 0, 1) }}
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="mb-0 fw-bold">{{ $session->astrologerProfile->display_name }}</h6>
                                            <small class="text-muted">{{ $session->updated_at->diffForHumans() }}</small>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <p class="mb-0 text-muted small text-truncate" style="max-width: 250px;">
                                                Click to continue conversation
                                            </p>
                                            @if($session->status === 'active')
                                                <span class="badge bg-success rounded-pill small"
                                                    style="font-size: 0.7rem;">Active</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="bi bi-chat-dots fs-1 text-muted"></i>
                                </div>
                                <p class="text-muted">No active chats found.</p>
                                <a href="{{ route('astrologers.index') }}" class="btn btn-primary btn-sm">Start a
                                    Conversation</a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection