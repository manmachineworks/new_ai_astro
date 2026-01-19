@props(['required', 'balance', 'action', 'route'])

@php
    $insufficient = $balance < $required;
@endphp

<div x-data="{ openGate: false }">
    <div @click="
        if({{ $insufficient ? 'true' : 'false' }}) { 
            $event.preventDefault(); 
            openGate = true; 
        }
    ">
        {{ $slot }}
    </div>

    @if($insufficient)
        <x-ui.modal name="wallet-gate-{{ Str::random(5) }}" :show="false" x-model="openGate" title="Low Balance">
            <div class="text-center p-4">
                <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4"
                    style="width: 80px; height: 80px;">
                    <i class="bi bi-wallet2 fs-1"></i>
                </div>
                <h4 class="fw-bold text-dark mb-3">Insufficient Balance</h4>
                <p class="text-muted mb-4">
                    You need at least <strong>₹{{ number_format($required, 2) }}</strong> to {{ $action }}.
                    Your current balance is <strong>₹{{ number_format($balance, 2) }}</strong>.
                </p>
                <div class="d-grid gap-2">
                    <a href="{{ route('user.wallet.recharge') }}" class="btn btn-primary py-2 fw-bold">
                        Recharge Wallet Now
                    </a>
                    <button type="button" class="btn btn-link text-muted text-decoration-none small"
                        @click="openGate = false">
                        Maybe Later
                    </button>
                </div>
            </div>
        </x-ui.modal>
    @endif
</div>