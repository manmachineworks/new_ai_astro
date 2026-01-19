@props([
    'variant' => 'neutral',
    'color' => null,
    'label' => null
])
@php
    // prioritizing color over variant if both provided
    $v = $color ?? $variant;

    $variants = [
        'success' => 'bg-success text-white',
        'warning' => 'bg-warning text-dark',
        'danger' => 'bg-danger text-white',
        'info' => 'bg-info text-dark',
        'neutral' => 'bg-secondary text-white',
        'secondary' => 'bg-secondary text-white',
        'dark' => 'bg-dark text-white',
        'light' => 'bg-light text-dark border',
        'primary' => 'bg-primary text-white',
        'blue' => 'bg-primary text-white',
        'green' => 'bg-success text-white',
        'red' => 'bg-danger text-white',
        'yellow' => 'bg-warning text-dark',
        'gray' => 'bg-secondary text-white',
    ];
    $class = $variants[$v] ?? $variants['neutral'];
@endphp

<span {{ $attributes->merge(['class' => "badge rounded-pill $class px-3 py-2"]) }}>
    {{ $label ?? $slot }}
</span>