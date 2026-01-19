@props(['thread'])

<div class="d-flex flex-column gap-4">
    @foreach($thread as $message)
        <div class="d-flex {{ $message['is_user'] ? 'justify-content-end' : 'justify-content-start' }}">
            <div class="d-flex {{ $message['is_user'] ? 'flex-row-reverse' : 'flex-row' }}" style="max-width: 85%;">
                <div class="flex-shrink-0 {{ $message['is_user'] ? 'ms-3' : 'me-3' }}">
                    <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center fw-bold text-muted"
                        style="width: 40px; height: 40px; font-size: 0.8rem;">
                        {{ substr($message['sender'] ?? 'U', 0, 1) }}
                    </div>
                </div>
                <div class="{{ $message['is_user'] ? 'text-end' : 'text-start' }}">
                    <div
                        class="card border-0 shadow-sm p-3 {{ $message['is_user'] ? 'bg-primary text-white' : 'bg-white border text-dark' }}">
                        <p class="text-sm mb-0">
                            {{ $message['message'] }}
                        </p>
                    </div>
                    <div class="mt-2 text-muted" style="font-size: 0.75rem;">
                        <i class="bi bi-clock me-1"></i>{{ $message['time'] }}
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>