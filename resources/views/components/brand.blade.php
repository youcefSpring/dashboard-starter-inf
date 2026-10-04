@props(['size' => 'md', 'subtitle' => true, 'inverse' => false])

@php
    $mark = $size === 'lg' ? 'size-11 rounded-xl' : 'size-9 rounded-[10px]';
    $glyph = $size === 'lg' ? 'size-6' : 'size-5';
@endphp

<span {{ $attributes->merge(['class' => 'flex min-w-0 items-center gap-3']) }}>
    <span @class(['grid shrink-0 place-items-center shadow-sm', $mark, 'brand-grad' => ! $inverse, 'bg-white/15 ring-1 ring-inset ring-white/25' => $inverse])>
        <svg class="{{ $glyph }}" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.6" stroke-linecap="round" aria-hidden="true">
            <path d="M5.5 10.5v3M9.5 7v10M13.5 4.5v15M17.5 8.5v7" /><circle cx="21" cy="12" r=".6" fill="#fff" />
        </svg>
    </span>
    <span class="sb-hide min-w-0">
        <span @class(['block text-[20px] font-extrabold lowercase leading-none tracking-tight', 'brand-word' => ! $inverse, 'text-white' => $inverse])>{{ config('app.name') }}</span>
        @if ($subtitle)
            <span @class(['mt-1 block text-xs font-medium', 'text-fg-3' => ! $inverse, 'text-white/75' => $inverse])>{{ __('Influence marketing') }}</span>
        @endif
    </span>
</span>
