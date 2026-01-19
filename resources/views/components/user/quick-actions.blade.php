<div class="row g-3">
    <div class="col-6 col-md-3">
        <a href="{{ route('user.astrologers.index') }}"
            class="card text-center text-decoration-none hover-shadow transition-all h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                    style="width: 48px; height: 48px;">
                    <i class="bi bi-telephone-fill fs-5"></i>
                </div>
                <h6 class="card-title text-dark mb-0 fw-semibold" style="font-size: 0.9rem;">Talk to Astrologer</h6>
            </div>
        </a>
    </div>

    <div class="col-6 col-md-3">
        <a href="{{ route('user.chat.index') }}"
            class="card text-center text-decoration-none hover-shadow transition-all h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                    style="width: 48px; height: 48px;">
                    <i class="bi bi-chat-dots-fill fs-5"></i>
                </div>
                <h6 class="card-title text-dark mb-0 fw-semibold" style="font-size: 0.9rem;">Chat with Astrologer</h6>
            </div>
        </a>
    </div>

    <div class="col-6 col-md-3">
        <a href="{{ route('user.ai.index') }}"
            class="card text-center text-decoration-none hover-shadow transition-all h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="bg-purple-subtle text-purple rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                    style="width: 48px; height: 48px; color: #6f42c1; background-color: rgba(111, 66, 193, 0.1);">
                    <i class="bi bi-robot fs-5"></i>
                </div>
                <h6 class="card-title text-dark mb-0 fw-semibold" style="font-size: 0.9rem;">AI Assistant</h6>
            </div>
        </a>
    </div>

    <div class="col-6 col-md-3">
        <a href="{{ route('user.horoscope.index') }}"
            class="card text-center text-decoration-none hover-shadow transition-all h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                    style="width: 48px; height: 48px;">
                    <i class="bi bi-stars fs-5"></i>
                </div>
                <h6 class="card-title text-dark mb-0 fw-semibold" style="font-size: 0.9rem;">Daily Horoscope</h6>
            </div>
        </a>
    </div>
</div>