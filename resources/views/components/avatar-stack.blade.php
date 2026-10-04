@props(['people' => [], 'max' => 3, 'size' => 'xs'])

{{-- Overlapping avatars. `people` is a list of ['name' => ..., 'tile' => ...]; extras collapse into a +N chip. --}}
@php
    $people = collect($people);
    $extra = max(0, $people->count() - $max);
@endphp

<span {{ $attributes->merge(['class' => 'flex items-center -space-x-2']) }} title="{{ $people->pluck('name')->implode(', ') }}">
    @foreach ($people->take($max) as $person)
        <x-avatar :name="$person['name']" :tile="$person['tile'] ?? null" :size="$size" class="rounded-full ring-2 ring-surface" />
    @endforeach
    @if ($extra)
        <span class="num grid h-6 min-w-6 place-items-center rounded-full bg-surface-2 px-1 text-[10px] font-bold text-fg-2 ring-2 ring-surface">+{{ $extra }}</span>
    @endif
    <span class="sr-only">{{ trans_choice(':count person|:count people', $people->count()) }}</span>
</span>
