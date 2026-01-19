@props(['type' => 'success', 'message', 'duration' => 3000])

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, {{ $duration }})" x-show="show"
        class="toast show align-items-center text-white bg-{{ $type === 'success' ? 'success' : 'danger' }} border-0"
        role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                {{ $message }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" @click="show = false"
                aria-label="Close"></button>
        </div>
    </div>
</div>