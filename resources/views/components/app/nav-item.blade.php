@props(['label', 'icon', 'href', 'active' => false, 'badge' => null])

<a href="{{ $href }}" @if ($active) aria-current="page" @endif data-tip="{{ $label }}" data-tip-side="right" data-tip-when="rail"
    @class([
        'sb-center relative flex h-10 items-center gap-3 rounded-lg px-3 text-sm font-semibold transition-colors',
        'bg-primary-soft text-primary-ink' => $active,
        'text-fg-2 hover:bg-surface-2 hover:text-fg' => ! $active,
    ])>
    <x-icon :name="$icon" class="size-[18px]" />
    <span class="sb-hide flex-1 truncate">{{ $label }}</span>
    @if ($badge)
        <span class="sb-hide num rounded-md bg-surface-3 px-1.5 text-[11px] font-bold leading-5 text-fg-2">{{ $badge }}</span>
    @endif
</a>
