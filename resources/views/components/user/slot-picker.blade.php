@props(['slots' => []])

<div x-data="{ selectedSlot: null }" class="row g-2">
    {{-- Mock Slots for now --}}
    @foreach(['10:00 AM', '10:30 AM', '11:00 AM', '02:00 PM', '02:30 PM', '03:00 PM', '05:00 PM', '05:30 PM'] as $slot)
        <div class="col-4 col-sm-3">
            <button type="button" @click="selectedSlot = '{{ $slot }}'"
                :class="selectedSlot === '{{ $slot }}' ? 'btn-primary shadow' : 'btn-outline-primary'"
                class="btn btn-sm w-100 py-2 fw-medium transition">
                {{ $slot }}
            </button>
        </div>
    @endforeach
    <input type="hidden" name="slot" x-model="selectedSlot" required>
</div>

<style>
    .transition {
        transition: all 0.2s ease;
    }
</style>