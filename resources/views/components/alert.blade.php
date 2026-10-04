@props(['tone' => 'success', 'icon' => null])

@php
    $styles = [
        'success' => ['bg-success/10 ring-success/20', 'text-success', 'circle-check'],
        'info' => ['bg-info/10 ring-info/20', 'text-info', 'info'],
        'warning' => ['bg-warning/10 ring-warning/25', 'text-warning', 'triangle-alert'],
        'error' => ['bg-error/10 ring-error/20', 'text-error', 'circle-alert'],
    ][$tone];
@endphp

<div role="{{ $tone === 'error' ? 'alert' : 'status' }}" {{ $attributes->merge(['class' => 'flex items-start gap-2.5 rounded-lg px-3.5 py-3 text-[13px] text-fg ring-1 ring-inset '.$styles[0]]) }}>
    <x-icon :name="$icon ?? $styles[2]" class="mt-px size-4 {{ $styles[1] }}" />
    <div class="min-w-0 flex-1">{{ $slot }}</div>
</div>
