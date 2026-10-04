@props(['label'])

<div>
    <p class="mb-2.5 text-xs font-semibold uppercase tracking-wide text-fg-3">{{ $label }}</p>
    <div {{ $attributes->merge(['class' => 'flex flex-wrap items-center gap-3']) }}>{{ $slot }}</div>
</div>
