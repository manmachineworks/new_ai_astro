@props(['notification'])

@php
    $icons = [
        'recharge' => 'bi bi-wallet2 text-success',
        'call' => 'bi bi-telephone-fill text-primary',
        'offer' => 'bi bi-gift-fill text-purple',
        'system' => 'bi bi-info-circle-fill text-secondary',
    ];
    $iconClass = $icons[$notification['type'] ?? 'system'] ?? $icons['system'];
    $isUnread = !($notification['read'] ?? true);
@endphp

<div
    class="list-group-item list-group-item-action border-0 border-bottom p-4 {{ $isUnread ? 'bg-primary bg-opacity-10' : '' }}">
    <div class="d-flex align-items-start gap-3">
        <div class="flex-shrink-0 bg-white shadow-sm p-2 rounded-3 d-flex align-items-center justify-content-center"
            style="width: 45px; height: 45px;">
            <i class="{{ $iconClass }} fs-4"></i>
        </div>
        <div class="flex-grow-1 min-width-0">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <h6 class="mb-0 fw-bold text-dark">{{ $notification['title'] ?? 'Notification' }}</h6>
                @if($isUnread)
                    <span class="badge bg-primary rounded-pill p-1">
                        <span class="visually-hidden">New</span>
                    </span>
                @endif
            </div>
            <p class="mb-2 text-muted small">
                {{ $notification['message'] ?? 'Notification message content.' }}
            </p>
            <div class="text-muted small opacity-75">
                <i class="bi bi-clock me-1"></i>{{ $notification['time'] ?? 'Just now' }}
            </div>
        </div>
    </div>
</div>

<style>
    .text-purple {
        color: #6f42c1 !important;
    }
</style>