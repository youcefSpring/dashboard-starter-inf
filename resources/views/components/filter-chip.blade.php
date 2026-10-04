@props(['count' => 0, 'active' => null])

{{-- Filter trigger with a selected-count badge. Use as an x-dropdown trigger (add data-dropdown-trigger) or a plain toggle. --}}
@php($active ??= $count > 0)

<button type="button" {{ $attributes->class([
    'inline-flex h-8 items-center gap-1.5 whitespace-nowrap rounded-lg border px-2.5 text-[13px] font-semibold transition-colors',
    'border-primary/40 bg-primary-soft text-primary-ink' => $active,
    'border-line bg-surface text-fg-2 hover:border-line-strong hover:text-fg' => ! $active,
]) }}>
    {{ $slot }}
    @if ($count)
        <span class="num rounded bg-primary px-1 text-[11px] leading-4 text-primary-on">{{ $count }}</span>
    @endif
    <x-icon name="chevron-down" class="size-3.5 opacity-70" />
</button>
