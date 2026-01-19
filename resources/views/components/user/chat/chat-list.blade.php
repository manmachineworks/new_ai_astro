@props(['sessions', 'activeSessionId' => null])

<div class="flex-grow-1 overflow-y-auto">
    <div class="list-group list-group-flush">
        @forelse($sessions as $session)
            <a href="{{ route('user.chat.show', $session['id']) }}"
                class="list-group-item list-group-item-action border-0 px-3 py-3 {{ $activeSessionId == $session['id'] ? 'bg-primary bg-opacity-10' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="position-relative flex-shrink-0">
                            <img class="rounded-circle" style="width: 45px; height: 45px; object-fit: cover;"
                                src="{{ $session['astrologer_image'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($session['astrologer_name']) . '&color=7F9CF5&background=EBF4FF' }}"
                                alt="">
                            @if($session['online'] ?? false)
                                <span class="position-absolute bottom-0 end-0 border border-white rounded-circle bg-success"
                                    style="width: 12px; height: 12px;"></span>
                            @endif
                        </div>
                        <div class="ms-3 overflow-hidden">
                            <h6 class="mb-0 fw-bold text-dark text-truncate" style="max-width: 150px;">
                                {{ $session['astrologer_name'] }}
                            </h6>
                            <p class="mb-0 text-muted small text-truncate" style="max-width: 150px;">
                                {{ $session['last_message'] }}
                            </p>
                        </div>
                    </div>
                    <div class="d-flex flex-column align-items-end flex-shrink-0 ms-2">
                        <small class="text-muted small mb-1">{{ $session['last_message_time'] }}</small>
                        @if(($session['unread'] ?? 0) > 0)
                            <span class="badge rounded-pill bg-primary">
                                {{ $session['unread'] }}
                            </span>
                        @endif
                    </div>
                </div>
            </a>
        @empty
            <div class="p-4 text-center text-muted small">
                No active chats.
            </div>
        @endforelse
    </div>
</div>