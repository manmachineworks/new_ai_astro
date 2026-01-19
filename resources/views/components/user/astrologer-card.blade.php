@props(['astrologer'])

<div class="card h-100 border-0 shadow-sm hover-shadow transition">
    <div class="card-body p-4">
        <div class="d-flex align-items-start gap-3">
            <div class="position-relative">
                <img src="{{ $astrologer['profile_image'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($astrologer['name'] ?? 'A') . '&color=7F9CF5&background=EBF4FF' }}"
                    alt="{{ $astrologer['name'] ?? 'Astrologer' }}"
                    class="rounded-circle border border-3 {{ ($astrologer['online'] ?? false) ? 'border-success' : 'border-light' }}"
                    style="width: 70px; height: 70px; object-fit: cover;">

                @if($astrologer['verified'] ?? false)
                    <span
                        class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle border border-2 border-white d-flex align-items-center justify-content-center shadow-sm"
                        style="width: 22px; height: 22px; font-size: 10px;">
                        <i class="bi bi-patch-check-fill"></i>
                    </span>
                @endif
            </div>

            <div class="flex-grow-1 min-width-0">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <h6 class="fw-bold mb-0 text-truncate pe-2">
                        <a href="{{ route('user.astrologers.show', $astrologer['id'] ?? 1) }}"
                            class="text-dark text-decoration-none stretched-link">
                            {{ $astrologer['name'] ?? 'Astrologer Name' }}
                        </a>
                    </h6>
                    <span class="badge bg-warning text-dark px-2 py-1 small">
                        <i class="bi bi-star-fill me-1"></i>{{ $astrologer['rating'] ?? '5.0' }}
                    </span>
                </div>

                <p class="text-muted small text-truncate mb-1">
                    {{ is_array($astrologer['specialties'] ?? '') ? implode(', ', $astrologer['specialties']) : ($astrologer['specialties'] ?? 'Specialties') }}
                </p>
                <p class="text-muted small text-truncate mb-3">
                    <i class="bi bi-translate me-1"></i>
                    {{ is_array($astrologer['languages'] ?? '') ? implode(', ', $astrologer['languages']) : ($astrologer['languages'] ?? 'Languages') }}
                </p>

                <div class="d-flex justify-content-between align-items-center">
                    <div class="fw-bold text-dark fs-5">₹{{ $astrologer['price_per_min'] ?? '0' }}<span
                            class="text-muted small fw-normal">/min</span></div>
                    <div class="text-muted small"><i
                            class="bi bi-briefcase me-1"></i>{{ $astrologer['experience'] ?? '5+' }} yrs</div>
                </div>
            </div>
        </div>

        <hr class="my-4 opacity-10">

        <div class="d-flex gap-2 position-relative z-index-10">
            <x-user.wallet-gate :required="($astrologer['price_per_min'] ?? 0) * 5"
                :balance="auth()->user()->wallet_balance ?? 0" :action="'call ' . ($astrologer['name'] ?? 'Astrologer')"
                :route="route('user.calls.dial', $astrologer['id'] ?? 1)">

                <a href="{{ route('user.calls.dial', $astrologer['id'] ?? 1) }}"
                    class="btn btn-outline-primary btn-sm px-3 fw-bold {{ !($astrologer['online'] ?? false) ? 'disabled' : '' }}">
                    <i class="bi bi-telephone-fill me-2"></i>Call
                </a>
            </x-user.wallet-gate>

            <a href="{{ route('user.chat.index') }}" class="btn btn-primary btn-sm px-3 fw-bold flex-grow-1">
                <i class="bi bi-chat-dots-fill me-2"></i>Chat
            </a>
        </div>
    </div>
</div>