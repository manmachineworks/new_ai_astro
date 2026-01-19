@props(['phone'])

<div x-data="{ timer: 30, canResend: false }"
    x-init="setInterval(() => { if(timer > 0) timer--; else canResend = true; }, 1000)" class="mt-4">
    <label class="form-label small fw-medium text-muted">Enter OTP sent to {{ $phone }}</label>
    <div class="mb-3">
        <input type="text" maxlength="6" class="form-control text-center fw-bold fs-4 py-2 shadow-sm border-2"
            style="letter-spacing: 0.5em;" placeholder="000000">
    </div>

    <div class="d-flex justify-content-between align-items-center">
        <span class="text-muted small" x-show="!canResend">
            <i class="bi bi-clock-history me-1"></i>Resend in <span x-text="timer" class="fw-bold"></span>s
        </span>
        <button type="button" x-show="canResend" @click="timer=30; canResend=false;"
            class="btn btn-link btn-sm text-primary text-decoration-none fw-bold p-0">
            Resend OTP
        </button>
    </div>
</div>