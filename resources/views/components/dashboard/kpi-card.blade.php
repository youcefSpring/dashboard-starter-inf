@props(['kpi'])

@php
    use Illuminate\Support\Number;

    $formatted = match ($kpi['format']) {
        'compact' => Number::abbreviate($kpi['value'], maxPrecision: 1),
        'percent' => number_format($kpi['value'], 2).'%',
        'money' => Number::currency($kpi['value'], precision: 0),
        default => number_format($kpi['value']),
    };

    $points = $kpi['points'] ?? false;
    $delta = $kpi['value'] - $kpi['previous'];
    $tone = ($kpi['neutral'] ?? false) ? 'primary' : ($delta >= 0 ? 'success' : 'error');

    // A gentle deterministic trend line for the sparkline, sloping with the change.
    $spark = collect(range(0, 13))
        ->map(fn (int $i): float => 50 + $i * ($delta >= 0 ? 2.4 : -1.8) + sin($i * 1.7 + strlen($kpi['label'])) * 6)
        ->all();
@endphp

<article class="flex min-w-0 flex-col rounded-xl border border-line bg-surface p-3.5 shadow-card sm:p-5">
    <div class="flex items-center justify-between gap-2">
        <div class="flex min-w-0 items-center gap-2.5">
            <span class="grid size-7 shrink-0 place-items-center rounded-lg bg-primary-soft text-primary-ink sm:size-8"><x-icon :name="$kpi['icon']" class="size-4" /></span>
            <h3 class="text-xs font-semibold leading-4 text-fg-2 sm:truncate sm:text-[13px]">{{ $kpi['label'] }}</h3>
        </div>
        <button type="button" data-tip="{{ $kpi['hint'] }}" aria-label="{{ __('About :metric', ['metric' => $kpi['label']]) }}"
            class="hidden size-6 shrink-0 place-items-center rounded-md text-fg-3 transition-colors hover:bg-surface-2 hover:text-fg sm:grid">
            <x-icon name="info" class="size-3.5" />
        </button>
    </div>

    <div class="mt-3 flex items-end justify-between gap-2 sm:mt-4">
        <div class="min-w-0">
            <p class="num truncate text-[22px] font-bold leading-7 tracking-tight sm:text-[28px] sm:leading-9">{{ $formatted }}</p>
            <div class="mt-1.5 flex flex-wrap items-center gap-x-1.5 gap-y-1">
                <x-change :current="$kpi['value']" :previous="$kpi['previous']" :points="$points" :neutral="$kpi['neutral'] ?? false" />
                <span class="text-xs text-fg-3"><span class="sm:hidden">{{ __('vs prev.') }}</span><span class="hidden sm:inline">{{ __('vs previous period') }}</span></span>
            </div>
        </div>
        <x-sparkline :values="$spark" :tone="$tone" class="hidden sm:block" />
    </div>
</article>
