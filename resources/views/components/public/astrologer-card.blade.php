@props(['astrologer'])

<div class="card h-100 border-0 shadow-sm astrologer-card">
    <div class="position-relative">
        <img src="{{ $astrologer->photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($astrologer->name ?? 'Astro') . '&size=400' }}"
            class="card-img-top object-fit-cover" style="height: 250px;" alt="{{ $astrologer->name }}">

        @if($astrologer->verified ?? false)
            <div class="verified-badge">
                <i class="bi bi-patch-check-fill"></i> Verified
            </div>
        @endif

        <div class="position-absolute bottom-0 end-0 p-2">
            <span class="badge {{ ($astrologer->online_call ?? false) ? 'bg-success' : 'bg-secondary' }}">
                <i class="bi bi-telephone-fill me-1"></i>
                {{ ($astrologer->online_call ?? false) ? 'Online' : 'Offline' }}
            </span>
        </div>
    </div>

    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <div>
                <h5 class="card-title fw-bold mb-0 text-truncate" style="max-width: 180px;">{{ $astrologer->name }}</h5>
                <small class="text-muted">{{ implode(', ', array_slice($astrologer->skills ?? [], 0, 2)) }}</small>
            </div>
            <div class="bg-warning-subtle text-warning-emphasis px-2 py-1 rounded fw-bold small">
                <i class="bi bi-star-fill"></i> {{ number_format($astrologer->rating ?? 0, 1) }}
            </div>
        </div>

        <p class="card-text small text-secondary mb-3">
            <i class="bi bi-translate me-1"></i> {{ implode(', ', array_slice($astrologer->languages ?? [], 0, 3)) }}
        </p>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="text-center">
                <small class="d-block text-muted" style="font-size: 0.7rem;">CHAT</small>
                <div class="fw-bold text-success">
                    @if(($astrologer->price_per_chat ?? 0) == 0)
                        FREE
                    @else
                        ₹{{ $astrologer->price_per_chat }}/min
                    @endif
                </div>
            </div>
            <div class="vr"></div>
            <div class="text-center">
                <small class="d-block text-muted" style="font-size: 0.7rem;">CALL</small>
                <div class="fw-bold text-success">
                    @if(($astrologer->price_per_min ?? 0) == 0)
                        FREE
                    @else
                        ₹{{ $astrologer->price_per_min }}/min
                    @endif
                </div>
            </div>
        </div>

        <div class="d-grid gap-2">
            <a href="{{ route('user.astrologers.show', $astrologer->id ?? 0) }}"
                class="btn btn-outline-primary btn-sm">View Profile</a>
            <div class="d-flex gap-2">
                <a href="#" class="btn btn-primary btn-sm flex-grow-1"><i class="bi bi-chat-dots-fill"></i> Chat</a>
                <a href="#" class="btn btn-success btn-sm flex-grow-1"><i class="bi bi-telephone-fill"></i> Call</a>
            </div>
        </div>
    </div>
</div>