@props(['items', 'suffix' => '%', 'color' => 'bg-c1'])

{{-- Horizontal bars with a label and value per row (e.g. age distribution). The largest value is emphasised. --}}
@php($max = max(array_column($items, 'value')) ?: 1)

<ul {{ $attributes->merge(['class' => 'space-y-2.5']) }}>
    @foreach ($items as $item)
        <li class="flex items-center gap-3 text-[13px]">
            <span class="num w-14 shrink-0 truncate text-fg-2">{{ $item['label'] }}</span>
            <span class="h-2 flex-1 rounded-full bg-surface-3">
                <span @class(['block h-full rounded-full', $color, 'opacity-45' => $item['value'] !== $max]) style="width: {{ $item['value'] / $max * 100 }}%"></span>
            </span>
            <b class="num w-10 shrink-0 text-right font-semibold">{{ $item['value'] }}{{ $suffix }}</b>
        </li>
    @endforeach
</ul>
