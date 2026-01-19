@extends('layouts.user')

@section('header')
    <x-ui.page-header title="Astrologer Profile" :breadcrumbs="[['label' => 'Astrologers', 'url' => route('user.astrologers.index')], ['label' => $astrologer['name'] ?? 'Astrologer']]" />
@endsection

@section('content')
    <div class="row g-4">
        {{-- Left Column: Profile Card --}}
        <div class="col-lg-8">
            <div class="d-flex flex-column gap-4">
                {{-- Main Profile Info --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <div class="row align-items-start">
                            <div class="col-md-auto text-center mb-4 mb-md-0">
                                <div class="position-relative d-inline-block">
                                    <img class="rounded-circle border border-4 {{ ($astrologer['online'] ?? false) ? 'border-success' : 'border-light' }} shadow-sm"
                                        style="width: 140px; height: 140px; object-fit: cover;"
                                        src="{{ $astrologer['profile_image'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($astrologer['name'] ?? 'A') . '&color=7F9CF5&background=EBF4FF' }}"
                                        alt="{{ $astrologer['name'] ?? 'Astrologer' }}">
                                    @if($astrologer['online'] ?? false)
                                        <span
                                            class="position-absolute bottom-0 end-0 bg-success border border-3 border-white rounded-circle p-2"
                                            title="Online"></span>
                                    @endif
                                </div>
                                <div class="mt-3">
                                    <x-ui.badge :color="($astrologer['online'] ?? false) ? 'success' : 'secondary'"
                                        :label="($astrologer['online'] ?? false) ? 'Online' : 'Offline'" />
                                </div>
                            </div>

                            <div class="col-md ms-md-4">
                                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                                    <h2 class="fw-bold text-dark mb-0">{{ $astrologer['name'] ?? 'Astrologer Name' }}</h2>
                                    <div class="d-flex align-items-center bg-warning bg-opacity-10 px-3 py-2 rounded-3">
                                        <i class="bi bi-star-fill text-warning me-2"></i>
                                        <span class="fw-bold text-dark">{{ $astrologer['rating'] ?? '5.0' }}</span>
                                        <span class="text-muted small ms-2">({{ $astrologer['rating_count'] ?? 0 }}
                                            reviews)</span>
                                    </div>
                                </div>

                                <p class="text-primary fw-medium mb-3">
                                    {{ is_array($astrologer['specialties'] ?? '') ? implode(', ', $astrologer['specialties']) : ($astrologer['specialties'] ?? 'Specialties') }}
                                </p>

                                <div class="d-flex flex-column gap-2 mb-4">
                                    <div class="d-flex align-items-center text-muted small">
                                        <i class="bi bi-translate me-2 text-primary"></i>
                                        {{ is_array($astrologer['languages'] ?? '') ? implode(', ', $astrologer['languages']) : ($astrologer['languages'] ?? 'English') }}
                                    </div>
                                    <div class="d-flex align-items-center text-muted small">
                                        <i class="bi bi-briefcase me-2 text-primary"></i>
                                        {{ $astrologer['experience'] ?? 5 }} Years Experience
                                    </div>
                                </div>

                                <div class="bg-light p-4 rounded-4">
                                    <h6 class="fw-bold text-dark mb-2">About</h6>
                                    <p class="text-muted small mb-0 line-height-lg">
                                        {{ $astrologer['bio'] ?? 'No biography available for this astrologer. They are an expert in their field and provide accurate readings.' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Reviews --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="fw-bold text-dark mb-4">Customer Reviews</h5>
                        <div class="py-4">
                            <x-ui.empty-state title="No reviews yet"
                                description="Be the first to share your experience with {{ $astrologer['name'] ?? 'this astrologer' }}." />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Actions --}}
        <div class="col-lg-4">
            <div class="d-flex flex-column gap-4">
                <x-user.pricing-panel :astrologer="$astrologer" />
                <x-user.availability-slots :astrologer="$astrologer" />
            </div>
        </div>
    </div>
@endsection

<style>
    .line-height-lg {
        line-height: 1.6;
    }
</style>