@props(['segments', 'height' => 'h-2.5', 'label' => null])

{{-- Proportional stacked bar. `segments` is a list of ['label', 'value', 'color' (c1|c2|c3|info|success|warning|error|primary|neutral)]. --}}
@php
    $colors = ['c1' => 'bg-c1', 'c2' => 'bg-c2', 'c3' => 'bg-c3', 'info' => 'bg-info', 'success' => 'bg-success', 'warning' => 'bg-warning', 'error' => 'bg-error', 'primary' => 'bg-primary', 'neutral' => 'bg-fg-3'];
    $total = array_sum(array_column($segments, 'value')) ?: 1;
@endphp

<div {{ $attributes->merge(['class' => 'flex w-full gap-0.5 overflow-hidden rounded-full '.$height]) }} role="img"
    aria-label="{{ $label ?? collect($segments)->map(fn ($s) => $s['label'].' '.$s['value'])->implode(', ') }}">
    @foreach ($segments as $segment)
        <span class="h-full first:rounded-l-full last:rounded-r-full {{ $colors[$segment['color']] }}" style="width: {{ $segment['value'] / $total * 100 }}%"
            data-tip="{{ $segment['label'] }}: {{ $segment['value'] }}"></span>
    @endforeach
</div>
