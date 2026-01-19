@props(['message'])

<div class="d-flex {{ $message['sender'] === 'user' ? 'justify-content-end' : 'justify-content-start' }}">
    <div class="d-flex flex-column {{ $message['sender'] === 'user' ? 'align-items-end' : 'align-items-start' }}"
        style="max-width: 75%;">
        <div
            class="px-3 py-2 rounded-3 text-sm shadow-sm {{ $message['sender'] === 'user' ? 'bg-primary text-white' : 'bg-white border text-dark' }}">
            <p class="mb-0">{{ $message['text'] }}</p>
        </div>
        <small class="text-muted mt-1 px-1" style="font-size: 0.75rem;">
            {{ $message['time'] }}
        </small>
    </div>
</div>