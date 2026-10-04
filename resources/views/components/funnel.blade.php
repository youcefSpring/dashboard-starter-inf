@props(['steps'])

{{-- Conversion funnel. `steps` is a list of ['label', 'value']; the step with the biggest drop-off is flagged. --}}
@php
    $first = $steps[0]['value'] ?: 1;
    $fills = ['bg-primary', 'bg-primary/80', 'bg-primary/65', 'bg-primary/50', 'bg-primary/40', 'bg-primary/30'];
    $drops = collect($steps)->map(fn ($step, $i) => $i ? 100 - ($step['value'] / max($steps[$i - 1]['value'], 1)) * 100 : 0)->all();
    $worst = array_search(max($drops), $drops, true);
@endphp

<ol {{ $attributes }}>
    @foreach ($steps as $i => $step)
        @if ($i)
            <li class="flex items-center justify-center gap-1.5 py-1.5 text-[11px] text-fg-3" aria-label="{{ number_format($drops[$i], 1) }}% {{ __('drop-off') }}">
                <x-icon name="arrow-down" class="size-3" />
                <span><span class="num font-semibold text-fg-2">{{ number_format(100 - $drops[$i], 1) }}%</span> {{ __('moved on') }}</span>
                <span @class(['num rounded px-1 font-semibold', 'bg-error/10 text-error' => $i === $worst, 'text-fg-2' => $i !== $worst])>
                    −{{ number_format($drops[$i], 1) }}%{{ $i === $worst ? ' '.__('biggest drop') : '' }}
                </span>
            </li>
        @endif
        <li>
            <div class="flex items-baseline justify-between gap-2">
                <span class="text-[13px] font-medium text-fg-2">{{ $step['label'] }}</span>
                <span class="flex items-baseline gap-2">
                    <b class="num text-sm font-bold">{{ number_format($step['value']) }}</b>
                    <span class="num w-11 text-right text-xs text-fg-3">{{ number_format($step['value'] / $first * 100, 1) }}%</span>
                </span>
            </div>
            <div class="mt-1.5 flex justify-center">
                <div class="h-3 rounded-md {{ $fills[min($i, count($fills) - 1)] }}" style="width: {{ $step['value'] / $first * 100 }}%"></div>
            </div>
        </li>
    @endforeach
</ol>
