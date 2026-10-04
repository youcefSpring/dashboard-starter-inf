@props(['label', 'icon', 'href', 'active' => false, 'badge' => null])

{{-- Horizontal-layout navigation link (the vertical sidebar uses x-app.nav-item). --}}
<a href="{{ $href }}" @if ($active) aria-current="page" @endif
    @class([
        'inline-flex h-9 shrink-0 items-center gap-2 whitespace-nowrap rounded-lg px-2.5 text-sm xl:px-3 font-semibold transition-colors',
        'bg-primary-soft text-primary-ink' => $active,
        'text-fg-2 hover:bg-surface-2 hover:text-fg' => ! $active,
    ])>
    <x-icon :name="$icon" />
    {{ $label }}
    @if ($badge)
        <span class="num rounded-md bg-surface-3 px-1.5 text-[11px] font-bold leading-5 text-fg-2">{{ $badge }}</span>
    @endif
</a>
