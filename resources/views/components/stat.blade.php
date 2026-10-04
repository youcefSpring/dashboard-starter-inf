@props(['label', 'value', 'variant' => 'plain', 'dot' => null, 'hint' => null])

{{--
    Label + value pair. `plain` for metric rows, `tile` for the tinted boxes (budget, ROI).
    Put an x-change (or anything) in the slot to show it under/next to the value.
--}}
<div {{ $attributes->class(['rounded-lg bg-surface-2 p-3' => $variant === 'tile', 'py-1' => $variant === 'plain']) }}>
    <dt class="flex items-center gap-1.5 text-xs font-medium text-fg-2">
        @if ($dot)<span class="size-2 rounded-full {{ $dot }}"></span>@endif
        {{ $label }}
        @if ($hint)
            <span class="text-fg-3" data-tip="{{ $hint }}" tabindex="0" aria-label="{{ $hint }}"><x-icon name="info" class="size-3" /></span>
        @endif
    </dt>
    <dd @class(['mt-1 flex flex-wrap items-center gap-x-2 gap-y-1'])>
        <span @class(['num font-bold tracking-tight', 'text-lg' => $variant === 'plain', 'text-base' => $variant === 'tile'])>{{ $value }}</span>
        {{ $slot }}
    </dd>
</div>
