@props(['current', 'previous', 'points' => false, 'neutral' => false, 'invert' => false])

{{-- Period-over-period change. `points` compares absolute values, `neutral` drops the good/bad colour, `invert` treats lower as better. --}}
@php
    $delta = $points ? $current - $previous : ($previous ? (($current - $previous) / $previous) * 100 : 0);
    $good = $invert ? -$delta : $delta;
    $tone = $neutral ? 'neutral' : ($good > 0.05 ? 'success' : ($good < -0.05 ? 'error' : 'neutral'));
    $arrow = $delta > 0.05 ? 'arrow-up-right' : ($delta < -0.05 ? 'arrow-down-right' : 'minus');
    $classes = [
        'success' => 'bg-success/10 text-success',
        'error' => 'bg-error/10 text-error',
        'neutral' => 'bg-surface-2 text-fg-2',
    ][$tone];
    $unit = $points ? ' pts' : '%';
@endphp

<span {{ $attributes->merge(['class' => 'num inline-flex items-center gap-0.5 rounded-md px-1.5 py-0.5 text-xs font-semibold '.$classes]) }}
    aria-label="{{ $delta > 0 ? 'Up' : ($delta < 0 ? 'Down' : 'No change') }} {{ number_format(abs($delta), 1) }}{{ $points ? ' points' : ' percent' }}">
    <x-icon :name="$arrow" class="size-3.5" stroke-width="2.25" />{{ $delta > 0 ? '+' : '' }}{{ number_format($delta, 1) }}{{ $unit }}
</span>
