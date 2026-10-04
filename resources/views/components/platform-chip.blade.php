@props(['platform', 'compact' => false])

@php
    [$label, $icon] = [
        'instagram' => ['Instagram', 'camera'],
        'tiktok' => ['TikTok', 'music-2'],
        'youtube' => ['YouTube', 'circle-play'],
        'x' => ['X', 'at-sign'],
    ][$platform] ?? [ucfirst($platform), 'globe'];
@endphp

@if ($compact)
    <span title="{{ $label }}" aria-label="{{ $label }}" {{ $attributes->merge(['class' => 'inline-grid size-5 place-items-center rounded-md bg-surface-2 text-fg-2 ring-1 ring-inset ring-line']) }}>
        <x-icon :name="$icon" class="size-3" />
    </span>
@else
    <span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 whitespace-nowrap rounded-md bg-surface-2 px-1.5 py-0.5 text-xs font-medium text-fg-2 ring-1 ring-inset ring-line']) }}>
        <x-icon :name="$icon" class="size-3.5" />{{ $label }}
    </span>
@endif
