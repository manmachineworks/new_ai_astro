@extends('admin.layouts.app')

@section('title', 'Chat Moderation')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold m-0 text-dark">Chat Moderation</h2>
            <p class="text-muted mb-0">Review conversations, mute access, and audit activity.</p>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.moderation.chats.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                        placeholder="User, phone, astrologer">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        @foreach(['active','closed','flagged'] as $status)
                            <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button class="btn btn-primary w-100">Filter</button>
                    <a href="{{ route('admin.moderation.chats.index') }}" class="btn btn-light w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Chats (7d)</div>
                    <div class="fw-bold">{{ array_sum($analytics['daily_counts'] ?? []) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Avg Response</div>
                    <div class="fw-bold">{{ $analytics['avg_response_seconds'] ? gmdate('i:s', $analytics['avg_response_seconds']) : 'N/A' }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Busiest Hours</div>
                    <div class="fw-bold">
                        @if(!empty($analytics['busiest_hours']))
                            {{ implode(', ', array_map(fn($h) => sprintf('%02d:00', $h), $analytics['busiest_hours'])) }}
                        @else
                            N/A
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white fw-bold">Conversations</div>
                <div class="list-group list-group-flush">
                    @forelse($threads as $thread)
                        <a href="{{ route('admin.moderation.chats.index', array_merge(request()->query(), ['thread_id' => $thread->id])) }}"
                            class="list-group-item list-group-item-action {{ $selectedThread && $selectedThread->id === $thread->id ? 'active' : '' }}">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="fw-bold">{{ $thread->user?->name ?? 'User' }}</div>
                                    <div class="small text-muted">Astrologer: {{ $thread->astrologer?->name ?? 'Astrologer' }}</div>
                                </div>
                                <div class="small text-muted">{{ optional($thread->last_message_at)->format('M d') }}</div>
                            </div>
                        </a>
                    @empty
                        <div class="list-group-item text-muted">No conversations found.</div>
                    @endforelse
                </div>
                @if($threads->hasPages())
                    <div class="card-footer bg-white">
                        {{ $threads->links() }}
                    </div>
                @endif
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-bold">Conversation</div>
                        @if($selectedThread)
                            <div class="small text-muted">
                                {{ $selectedThread->user?->name ?? 'User' }} · {{ $selectedThread->astrologer?->name ?? 'Astrologer' }}
                            </div>
                        @endif
                    </div>
                    @if($selectedThread)
                        <div class="d-flex gap-2">
                            <form method="POST" action="{{ route('admin.moderation.chats.mute', $selectedThread) }}"
                                data-confirm data-confirm-title="Mute User"
                                data-confirm-text="Mute this user from chat for 24 hours?">
                                @csrf
                                <input type="hidden" name="target" value="user">
                                <input type="hidden" name="duration_minutes" value="1440">
                                <button class="btn btn-sm btn-outline-warning">Mute User (24h)</button>
                            </form>
                            <form method="POST" action="{{ route('admin.moderation.chats.mute', $selectedThread) }}"
                                data-confirm data-confirm-title="Mute Astrologer"
                                data-confirm-text="Mute this astrologer from chat for 24 hours?">
                                @csrf
                                <input type="hidden" name="target" value="astrologer">
                                <input type="hidden" name="duration_minutes" value="1440">
                                <button class="btn btn-sm btn-outline-warning">Mute Astrologer (24h)</button>
                            </form>
                            <form method="POST" action="{{ route('admin.moderation.chats.unmute', [$selectedThread, 'user']) }}"
                                data-confirm data-confirm-title="Unmute User"
                                data-confirm-text="Remove chat restriction for this user?">
                                @csrf
                                <button class="btn btn-sm btn-outline-success">Unmute User</button>
                            </form>
                            <form method="POST" action="{{ route('admin.moderation.chats.unmute', [$selectedThread, 'astrologer']) }}"
                                data-confirm data-confirm-title="Unmute Astrologer"
                                data-confirm-text="Remove chat restriction for this astrologer?">
                                @csrf
                                <button class="btn btn-sm btn-outline-success">Unmute Astrologer</button>
                            </form>
                        </div>
                    @endif
                </div>
                <div class="card-body" style="max-height: 520px; overflow-y: auto;">
                    @if($selectedThread && $messages->count())
                        @foreach($messages as $message)
                            <div class="mb-3">
                                <div class="small text-muted">
                                    {{ $message->sender?->name ?? 'User' }} · {{ $message->created_at->format('M d, H:i') }}
                                </div>
                                <div class="p-2 bg-light rounded">
                                    {{ $message->message }}
                                </div>
                            </div>
                        @endforeach
                    @elseif($selectedThread)
                        <div class="text-muted">No stored messages for this conversation.</div>
                    @else
                        <div class="text-muted">Select a conversation to view messages.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
