@extends('layouts.user')

@section('content')
    <div class="d-flex align-items-center justify-content-center min-vh-75">
        <div class="w-100" style="max-width: 450px;">
            <x-user.call-status :astrologerName="$astrologer['name'] ?? 'Astrologer'" status="calling" />

            <div class="mt-4 text-center">
                <p class="text-muted mb-1">Please wait while we connect your call...</p>
                <p class="text-muted small">Ensure your phone is reachable and has a stable network.</p>
            </div>
        </div>
    </div>
@endsection

<style>
    .min-vh-75 {
        min-height: 75vh;
    }
</style>