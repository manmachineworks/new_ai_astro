@props(['balance'])

<div class="card bg-primary text-white border-0 shadow rounded-4 overflow-hidden position-relative">
    <div class="card-body p-4 position-relative z-2">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <p class="text-white-50 small fw-medium mb-1">Available Balance</p>
                <h3 class="fw-bold mb-0">₹{{ number_format($balance, 2) }}</h3>
            </div>
            <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center"
                style="width: 48px; height: 48px;">
                <i class="bi bi-wallet2 fs-4 text-white"></i>
            </div>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('user.wallet.recharge') }}" class="btn btn-light text-primary flex-fill fw-semibold">
                + Add Money
            </a>
            <a href="{{ route('user.wallet.transactions') }}" class="btn btn-outline-light flex-fill fw-semibold">
                History
            </a>
        </div>
    </div>

    <!-- Decorative circles -->
    <div class="position-absolute top-0 end-0 translate-middle p-5 bg-white opacity-10 rounded-circle"></div>
    <div class="position-absolute bottom-0 start-0 translate-middle p-5 bg-white opacity-10 rounded-circle"></div>
</div>