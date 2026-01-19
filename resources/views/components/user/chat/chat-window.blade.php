@props(['session', 'messages'])

<div class="h-100 d-flex flex-column bg-white">
    {{-- Header --}}
    <div class="card-header bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center shadow-sm position-relative"
        style="z-index: 10;">
        <div class="d-flex align-items-center">
            <div class="d-md-none me-3">
                <a href="{{ route('user.chat.index') }}" class="text-secondary text-decoration-none">
                    <i class="bi bi-chevron-left fs-4"></i>
                </a>
            </div>
            <img class="rounded-circle" style="width: 35px; height: 35px; object-fit: cover;"
                src="{{ $session['astrologer_image'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($session['astrologer_name']) . '&color=7F9CF5&background=EBF4FF' }}"
                alt="">
            <div class="ms-3">
                <h6 class="mb-0 fw-bold text-dark">{{ $session['astrologer_name'] }}</h6>
                <small class="text-{{ $session['online'] ? 'success' : 'muted' }} small">
                    {{ $session['online'] ? 'Online' : 'Offline' }}
                </small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-outline-danger border-0 fw-medium">
                End Chat
            </button>
        </div>
    </div>

    {{-- Messages Area --}}
    <div class="flex-grow-1 overflow-y-auto p-4 bg-light" id="chat-messages" style="scroll-behavior: smooth;">
        <div class="d-flex flex-column gap-3">
            @foreach($messages as $message)
                <x-user.chat.message-bubble :message="$message" />
            @endforeach
        </div>
    </div>

    {{-- Composer --}}
    <div class="card-footer bg-white border-top p-3">
        <x-user.chat.composer />
    </div>
</div>