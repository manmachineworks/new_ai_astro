@extends('layouts.user')

@section('header')
    <x-ui.page-header title="Book Appointment" :breadcrumbs="[['label' => 'Astrologers', 'url' => route('user.astrologers.index')], ['label' => $astrologer['name'] ?? 'Astrologer', 'url' => route('user.astrologers.show', $astrologer['id'] ?? 1)], ['label' => 'Book']]" />
@endsection

@section('content')
    <div class="mx-auto" style="max-width: 800px;">
        <form action="{{ route('user.appointments.confirm') }}" method="POST">
            @csrf
            <input type="hidden" name="astrologer_id" value="{{ $astrologer['id'] ?? 1 }}">

            {{-- Astrologer Summary --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <img class="rounded-circle" style="width: 70px; height: 70px; object-fit: cover;"
                        src="{{ $astrologer['profile_image'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($astrologer['name'] ?? 'A') }}"
                        alt="">
                    <div>
                        <h4 class="fw-bold text-dark mb-1">{{ $astrologer['name'] ?? 'Astrologer Name' }}</h4>
                        <p class="text-muted small mb-1">
                            {{ is_array($astrologer['specialties'] ?? '') ? implode(', ', $astrologer['specialties']) : ($astrologer['specialties'] ?? 'Specialties') }}
                        </p>
                        <div class="fw-bold text-primary">₹{{ $astrologer['price_per_min'] ?? '0' }}/min</div>
                    </div>
                </div>
            </div>

            {{-- Date Selection --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-4">Select Date</h5>
                    <div class="d-flex gap-3 overflow-auto pb-2 custom-scrollbar">
                        @foreach(['Today', 'Tomorrow', 'Fri, 30 Oct', 'Sat, 31 Oct'] as $index => $date)
                            <div class="flex-shrink-0">
                                <input type="radio" name="date" value="{{ $date }}" class="btn-check" id="date_{{ $index }}" {{ $index === 0 ? 'checked' : '' }}>
                                <label class="btn btn-outline-primary px-4 py-3 border-2 fw-bold" for="date_{{ $index }}">
                                    {{ $date }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Slot Selection --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-4">Select Time Slot</h5>
                    <x-user.slot-picker />
                </div>
            </div>

            {{-- Summary & Confirm --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small">Consultation Fee (30 mins)</span>
                        <span class="fw-bold text-dark">₹{{ ($astrologer['price_per_min'] ?? 0) * 30 }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small">GST (18%)</span>
                        <span class="fw-bold text-dark">₹{{ (($astrologer['price_per_min'] ?? 0) * 30) * 0.18 }}</span>
                    </div>
                    <hr class="my-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="h5 fw-bold text-dark mb-0">Total Amount</span>
                        <span
                            class="h4 fw-bold text-primary mb-0">₹{{ (($astrologer['price_per_min'] ?? 0) * 30) * 1.18 }}</span>
                    </div>

                    <x-user.wallet-gate :required="(($astrologer['price_per_min'] ?? 0) * 30) * 1.18"
                        :balance="auth()->user()->wallet_balance ?? 0" :action="'book appointment'" :route="'#'">
                        <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-bold shadow-sm">
                            Confirm Booking
                        </button>
                    </x-user.wallet-gate>
                </div>
            </div>
        </form>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            height: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 10px;
        }

        .btn-check:checked+.btn-outline-primary {
            background-color: var(--bs-primary);
            color: white;
            box-shadow: 0 4px 10px rgba(var(--bs-primary-rgb), 0.3);
        }
    </style>
@endsection