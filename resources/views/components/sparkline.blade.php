@props(['values', 'tone' => 'success'])

{{-- Tiny trend line (88×32). Tones: success, error, primary, neutral. Drawn by resources/js/charts.js. --}}
<div data-sparkline="{{ json_encode(array_values($values)) }}" data-tone="{{ $tone }}" {{ $attributes->merge(['class' => 'h-8 w-[88px]']) }}></div>
