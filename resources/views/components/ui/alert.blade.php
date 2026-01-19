@props(['type' => 'info', 'message'])

@php
    $typeMap = [
        'info' => 'primary',
        'success' => 'success',
        'warning' => 'warning',
        'error' => 'danger',
    ];
    $bsType = $typeMap[$type] ?? 'primary';

    $iconMap = [
        'info' => 'bi-info-circle-fill',
        'success' => 'bi-check-circle-fill',
        'warning' => 'bi-exclamation-triangle-fill',
        'error' => 'bi-exclamation-circle-fill',
    ];
    $icon = $iconMap[$type] ?? 'bi-info-circle-fill';
@endphp

<div class="alert alert-{{ $bsType }} d-flex align-items-center" role="alert">
    <i class="bi {{ $icon }} flex-shrink-0 me-2 fs-5"></i>
    <div>
        {{ $message }}
    </div>
</div>