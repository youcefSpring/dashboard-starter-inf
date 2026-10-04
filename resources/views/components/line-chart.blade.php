@props(['labels', 'series', 'toggles' => true, 'label' => null])

{{--
    Interactive multi-series line chart with hover/keyboard tooltip.
    `labels` are ISO dates; `series` is a list of ['key', 'label', 'color' (c1|c2|c3|info|success|…), 'values'].
    The first visible series sets the y-axis; the others are scaled to fit.
--}}
@php
    $dots = ['c1' => 'bg-c1', 'c2' => 'bg-c2', 'c3' => 'bg-c3', 'info' => 'bg-info', 'success' => 'bg-success', 'warning' => 'bg-warning', 'error' => 'bg-error', 'primary' => 'bg-primary'];
    $payload = [
        'labels' => array_values($labels),
        'series' => collect($series)->map(fn (array $s): array => [
            'key' => $s['key'],
            'label' => $s['label'],
            'color' => $s['color'],
            'dot' => $dots[$s['color']] ?? 'bg-c1',
            'values' => array_values($s['values']),
        ])->values()->all(),
    ];
@endphp

<div data-line-chart-root {{ $attributes->merge(['class' => 'flex flex-col']) }}>
    @if ($toggles && count($series) > 1)
        <div class="flex flex-wrap gap-2" role="group" aria-label="{{ __('Show or hide series') }}">
            @foreach ($payload['series'] as $s)
                <button type="button" data-series-toggle="{{ $s['key'] }}" aria-pressed="true"
                    class="group inline-flex h-8 items-center gap-2 rounded-lg border border-dashed border-line-strong px-2.5 text-[13px] font-semibold text-fg-3 transition-colors hover:text-fg-2 aria-pressed:border-solid aria-pressed:border-line aria-pressed:bg-surface aria-pressed:text-fg">
                    <span class="size-2.5 rounded-full opacity-30 group-aria-pressed:opacity-100 {{ $s['dot'] }}"></span>{{ $s['label'] }}
                </button>
            @endforeach
        </div>
    @endif

    <div data-line-chart="{{ json_encode($payload) }}" tabindex="0" role="img"
        aria-label="{{ $label ?? __('Line chart') }}. {{ __('Use the left and right arrow keys to read values.') }}"
        class="relative mt-4 min-h-[240px] flex-1 sm:min-h-[280px]"></div>
</div>
