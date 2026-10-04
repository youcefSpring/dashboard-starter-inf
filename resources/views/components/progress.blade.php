@props(['value', 'tone' => 'primary', 'height' => 'h-1.5', 'marker' => null, 'label' => null])

@php
    $fill = [
        'primary' => 'bg-primary',
        'success' => 'bg-success',
        'warning' => 'bg-warning',
        'error' => 'bg-error',
        'brand' => 'brand-grad',
    ][$tone];
@endphp

<div {{ $attributes->merge(['class' => 'relative w-full rounded-full bg-surface-3 '.$height]) }}
    role="progressbar" aria-valuenow="{{ (int) round($value) }}" aria-valuemin="0" aria-valuemax="100" @if ($label) aria-label="{{ $label }}" @endif>
    <div class="h-full rounded-full {{ $fill }}" style="width: {{ max(0, min(100, $value)) }}%"></div>
    @if ($marker !== null)
        <span class="absolute -top-1 -bottom-1 w-0.5 rounded-full bg-fg" style="left: {{ $marker }}%" aria-hidden="true"></span>
    @endif
</div>
