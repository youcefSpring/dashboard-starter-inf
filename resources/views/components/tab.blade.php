@props(['active' => false, 'href' => null, 'target' => null, 'icon' => null, 'count' => null, 'size' => 'sm'])

@php
    $classes = 'inline-flex items-center gap-1.5 whitespace-nowrap rounded-md font-semibold transition-colors '
        .($size === 'sm' ? 'h-7 px-2.5 text-[13px] ' : 'h-8 px-3 text-sm ')
        .'aria-selected:bg-surface aria-selected:text-fg aria-selected:shadow-card text-fg-2 hover:text-fg';
@endphp

@if ($href)
    <a href="{{ $href }}" role="tab" aria-selected="{{ $active ? 'true' : 'false' }}" {{ $attributes->merge(['class' => $classes]) }}>
@else
    <button type="button" role="tab" aria-selected="{{ $active ? 'true' : 'false' }}" tabindex="{{ $active ? 0 : -1 }}"
        @if ($target) data-tab-target="{{ $target }}" aria-controls="{{ $target }}" @endif {{ $attributes->merge(['class' => $classes]) }}>
@endif
        @if ($icon)<x-icon :name="$icon" class="size-3.5" />@endif
        {{ $slot }}
        @if ($count !== null)
            <span @class(['num rounded px-1 text-[11px]', 'bg-primary-soft text-primary-ink' => $active, 'bg-surface-3 text-fg-2' => ! $active])>{{ $count }}</span>
        @endif
@if ($href)
    </a>
@else
    </button>
@endif
