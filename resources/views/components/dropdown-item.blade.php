@props(['icon' => null, 'href' => null, 'danger' => false])

@php
    $classes = 'flex w-full items-center gap-2.5 rounded-lg px-2.5 py-2 text-left text-sm font-medium transition-colors '
        .($danger ? 'text-error hover:bg-error/10' : 'text-fg hover:bg-surface-2');
@endphp

@if ($href)
    <a href="{{ $href }}" role="menuitem" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)<x-icon :name="$icon" @class(['size-4', 'text-fg-3' => ! $danger]) />@endif
        <span class="flex-1">{{ $slot }}</span>
        {{ $right ?? '' }}
    </a>
@else
    <button type="{{ $attributes->get('type', 'button') }}" role="menuitem" {{ $attributes->except('type')->merge(['class' => $classes]) }}>
        @if ($icon)<x-icon :name="$icon" @class(['size-4', 'text-fg-3' => ! $danger]) />@endif
        <span class="flex-1">{{ $slot }}</span>
        {{ $right ?? '' }}
    </button>
@endif
