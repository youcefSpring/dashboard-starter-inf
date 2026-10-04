@props(['values', 'labels', 'suffix' => '', 'prefix' => ''])

{{-- Compact line with a dot per point and labels underneath (e.g. ROI by month). Drawn by resources/js/charts.js. --}}
<div {{ $attributes }}>
    <div class="relative h-16" data-mini-line="{{ json_encode(['values' => array_values($values), 'labels' => array_values($labels), 'prefix' => $prefix, 'suffix' => $suffix]) }}"></div>
    <div class="mt-2 flex justify-between text-[11px] text-fg-3">
        @foreach ($labels as $label)
            <span @class(['font-semibold text-fg' => $loop->last])>{{ $label }}</span>
        @endforeach
    </div>
</div>
