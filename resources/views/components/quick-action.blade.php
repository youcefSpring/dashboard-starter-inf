@props(['icon', 'href' => null])

{{-- Icon + label tile for a grid of shortcuts (e.g. <div class="grid grid-cols-3 gap-2">). --}}
@php($classes = 'group flex flex-col items-center gap-2 rounded-lg border border-line px-2 py-3 text-center text-xs font-semibold text-fg transition-colors hover:border-primary/40 hover:bg-primary-soft')

@if ($href)<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>@else<button type="button" {{ $attributes->merge(['class' => $classes]) }}>@endif
    <span class="grid size-9 place-items-center rounded-lg bg-surface-2 text-primary-ink transition-colors group-hover:bg-surface"><x-icon :name="$icon" class="size-[18px]" /></span>
    <span class="leading-tight">{{ $slot }}</span>
@if ($href)</a>@else</button>@endif
