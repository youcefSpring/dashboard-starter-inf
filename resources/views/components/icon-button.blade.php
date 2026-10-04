@props(['icon', 'label', 'size' => 'md', 'href' => null])

@php
    $classes = 'relative grid shrink-0 place-items-center rounded-lg text-fg-2 transition-colors hover:bg-surface-2 hover:text-fg '
        .($size === 'sm' ? 'size-8' : 'size-10');
    $iconSize = $size === 'sm' ? 'size-4' : 'size-[18px]';
@endphp

@if ($href)
    <a href="{{ $href }}" aria-label="{{ $label }}" data-tip="{{ $label }}" {{ $attributes->merge(['class' => $classes]) }}>
        <x-icon :name="$icon" :class="$iconSize" />{{ $slot }}
    </a>
@else
    <button type="button" aria-label="{{ $label }}" data-tip="{{ $label }}" {{ $attributes->merge(['class' => $classes]) }}>
        <x-icon :name="$icon" :class="$iconSize" />{{ $slot }}
    </button>
@endif
