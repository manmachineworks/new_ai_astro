@props(['astrologer'])

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <h5 class="fw-bold text-dark mb-4">Pricing</h5>
        <div class="d-flex flex-column gap-3 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted small">
                    <i class="bi bi-telephone-fill me-2 text-primary"></i>Voice Call
                </span>
                <span class="fw-bold text-dark">₹{{ $astrologer['price_per_min'] }}/min</span>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted small">
                    <i class="bi bi-chat-dots-fill me-2 text-success"></i>Chat
                </span>
                <span class="fw-bold text-dark">₹{{ $astrologer['chat_price_per_min'] }}/min</span>
            </div>
        </div>

        <div class="d-grid gap-2">
            <x-user.wallet-gate 
                :required="$astrologer['price_per_min'] * 5"
                :balance="auth()->user()->wallet_balance ?? 0"
                :action="'call'"
                :route="route('user.calls.dial', $astrologer['id'])">
                <a href="{{ route('user.calls.dial', $astrologer['id']) }}" class="btn btn-outline-primary w-100 fw-bold py-2">
                    <i class="bi bi-telephone-outbound-fill me-2"></i>Call Now
                </a>
            </x-user.wallet-gate>

            <a href="{{ route('user.chat.index') }}" class="btn btn-success fw-bold py-2">
                <i class="bi bi-chat-fill me-2"></i>Start Chat
            </a>
        </div>
    </div>
</div>
