@props(['segments', 'size' => 148, 'stroke' => 16])

{{-- Donut chart. `segments` is a list of ['label', 'value', 'color' (c1|c2|c3|info|success|warning|error|primary)]. The slot renders in the centre. --}}
@php
    $radius = ($size - $stroke) / 2;
    $circumference = 2 * M_PI * $radius;
    $total = array_sum(array_column($segments, 'value')) ?: 1;
    $gap = 2.5;
    $offset = 0;
@endphp

<div {{ $attributes->merge(['class' => 'relative shrink-0']) }} style="width: {{ $size }}px; height: {{ $size }}px">
    <svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 {{ $size }} {{ $size }}" class="-rotate-90" role="img"
        aria-label="{{ collect($segments)->map(fn ($s) => $s['label'].' '.$s['value'])->implode(', ') }}">
        <circle cx="{{ $size / 2 }}" cy="{{ $size / 2 }}" r="{{ $radius }}" fill="none" stroke-width="{{ $stroke }}" style="stroke: rgb(var(--surface-2))" />
        @foreach ($segments as $segment)
            @php
                $length = $segment['value'] / $total * $circumference;
                $dash = max($length - $gap, 0.5);
            @endphp
            <circle cx="{{ $size / 2 }}" cy="{{ $size / 2 }}" r="{{ $radius }}" fill="none" stroke-width="{{ $stroke }}"
                style="stroke: rgb(var(--{{ $segment['color'] }}))" stroke-dasharray="{{ $dash }} {{ $circumference - $dash }}" stroke-dashoffset="{{ -$offset }}">
                <title>{{ $segment['label'] }}: {{ $segment['value'] }}</title>
            </circle>
            @php($offset += $length)
        @endforeach
    </svg>
    <div class="absolute inset-0 grid place-items-center text-center">{{ $slot }}</div>
</div>
