@props([
    'variant' => 'secondary',
    'size' => 'md',
    'icon' => null,
    'iconRight' => null,
    'href' => null,
    'type' => 'button',
])

@php
    $sizes = [
        'sm' => 'h-8 gap-1.5 px-3 text-[13px]',
        'md' => 'h-10 gap-2 px-4 text-sm',
        'lg' => 'h-11 gap-2 px-5 text-sm',
    ];

    $variants = [
        'primary' => 'bg-primary text-primary-on shadow-sm hover:bg-primary-hover',
        'secondary' => 'border border-line bg-surface text-fg shadow-card hover:border-line-strong hover:bg-surface-2',
        'ghost' => 'text-fg-2 hover:bg-surface-2 hover:text-fg',
        'soft' => 'bg-primary-soft text-primary-ink hover:bg-primary/15',
        'danger' => 'bg-error text-white hover:bg-error/90',
    ];

    $classes = 'inline-flex items-center justify-center whitespace-nowrap rounded-lg font-semibold transition-colors disabled:cursor-not-allowed disabled:opacity-50 '
        .$sizes[$size].' '.$variants[$variant];

    $iconSize = $size === 'sm' ? 'size-4' : 'size-[18px]';
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)<x-icon :name="$icon" :class="$iconSize" />@endif
        <span>{{ $slot }}</span>
        @if ($iconRight)<x-icon :name="$iconRight" class="size-4 opacity-70" />@endif
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)<x-icon :name="$icon" :class="$iconSize" data-button-icon />@endif
        <span>{{ $slot }}</span>
        @if ($iconRight)<x-icon :name="$iconRight" class="size-4 opacity-70" />@endif
    </button>
@endif
