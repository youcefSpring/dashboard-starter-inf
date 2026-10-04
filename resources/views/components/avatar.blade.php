@props(['name', 'tile' => null, 'size' => 'md', 'square' => false, 'online' => false, 'ring' => false])

@php
    $tiles = config('ui.tiles');

    // Without an explicit tile, derive a stable colour from the name.
    $tile ??= array_keys($tiles)[crc32($name) % count($tiles)];

    $sizes = [
        'xs' => 'size-6 text-[10px]',
        'sm' => 'size-8 text-xs',
        'md' => 'size-10 text-sm',
        'lg' => 'size-12 text-base',
        'xl' => 'size-16 text-xl',
    ];

    $initials = collect(preg_split('/\s+/u', trim(preg_replace('/[^\p{L}\s]/u', '', $name))))
        ->filter()
        ->take(2)
        ->map(fn (string $word): string => mb_strtoupper(mb_substr($word, 0, 1)))
        ->implode('');
@endphp

<span {{ $attributes->merge(['class' => 'relative inline-flex shrink-0']) }}>
    @if ($ring)
        {{-- Brand-gradient story ring, reserved for creators. --}}
        <span class="brand-grad rounded-full p-[2px]"><span class="block rounded-full bg-surface p-[2px]">
    @endif
    <span @class([
        'grid place-items-center font-bold tracking-tight',
        $sizes[$size],
        $tiles[$tile],
        'rounded-lg' => $square,
        'rounded-full' => ! $square,
    ]) aria-hidden="true">{{ $initials }}</span>
    @if ($ring)
        </span></span>
    @endif
    @if ($online)
        <span class="absolute bottom-0 right-0 size-2.5 rounded-full bg-success ring-2 ring-surface" title="Online"></span>
    @endif
</span>
