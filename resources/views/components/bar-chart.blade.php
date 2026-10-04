@props(['items', 'height' => 'h-24'])

{{-- Vertical bars; the last bar is highlighted. `items` is a list of ['label', 'value', 'display' (optional formatted value)]. --}}
@php($max = max(array_column($items, 'value')) ?: 1)

<div {{ $attributes->merge(['class' => 'flex items-end gap-2 '.$height]) }} role="img"
    aria-label="{{ collect($items)->map(fn ($item) => $item['label'].' '.($item['display'] ?? $item['value']))->implode(', ') }}">
    @foreach ($items as $item)
        <div class="flex h-full flex-1 flex-col items-center gap-1.5">
            <div class="flex w-full flex-1 items-end">
                <div @class(['w-full rounded-t-md transition-colors', 'bg-primary' => $loop->last, 'bg-primary/20 hover:bg-primary/35' => ! $loop->last])
                    style="height: {{ $item['value'] / $max * 100 }}%" data-tip="{{ $item['label'] }}: {{ $item['display'] ?? $item['value'] }}"></div>
            </div>
            <span @class(['text-[11px]', 'font-semibold text-fg' => $loop->last, 'text-fg-3' => ! $loop->last])>{{ $item['label'] }}</span>
        </div>
    @endforeach
</div>
