@props(['label' => null])

{{--
    Segmented tab list. Children are x-tab items: links (href) for server-side switching,
    or buttons (target) that show the matching x-tab-panel without a page load.
--}}
<div role="tablist" @if ($label) aria-label="{{ $label }}" @endif data-tabs
    {{ $attributes->merge(['class' => 'inline-flex max-w-full items-center gap-0.5 overflow-x-auto rounded-lg border border-line bg-surface-2 p-0.5 no-scrollbar']) }}>
    {{ $slot }}
</div>
