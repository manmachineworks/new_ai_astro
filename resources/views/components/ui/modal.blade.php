@props(['show' => false, 'title' => 'Confirm', 'maxWidth' => 'md'])

@php
    $maxWidthClass = [
        'sm' => 'modal-sm',
        'md' => '',
        'lg' => 'modal-lg',
        'xl' => 'modal-xl',
        '2xl' => 'modal-xl',
    ][$maxWidth] ?? '';
@endphp

<div x-data="{ show: @js($show) }" x-show="show"
    x-on:open-modal.window="$event.detail == '{{ $attributes->get('name') }}' ? show = true : null"
    x-on:close-modal.window="$event.detail == '{{ $attributes->get('name') }}' ? show = false : null"
    x-on:keydown.escape.window="show = false" class="modal fade" :class="{ 'show d-block': show }"
    style="background: rgba(0,0,0,0.5);" tabindex="-1" aria-modal="true" role="dialog">

    <div class="modal-dialog modal-dialog-centered {{ $maxWidthClass }}">
        <div class="modal-content" @click.away="show = false">
            <div class="modal-header">
                <h5 class="modal-title">{{ $title }}</h5>
                <button type="button" class="btn-close" @click="show = false" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                {{ $slot }}
            </div>

            @if(isset($footer))
                <div class="modal-footer">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>