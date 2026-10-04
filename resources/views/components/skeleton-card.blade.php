@props(['rows' => 4])

<div aria-hidden="true" {{ $attributes->merge(['class' => 'flex flex-col gap-4 rounded-xl border border-line bg-surface p-5 shadow-card']) }}>
    <div class="flex items-center justify-between">
        <x-skeleton class="h-4 w-40" />
        <x-skeleton class="h-8 w-24 rounded-lg" />
    </div>
    <x-skeleton class="h-3 w-64 max-w-full" />
    <div class="mt-2 flex flex-1 flex-col justify-end gap-3">
        @foreach (range(1, $rows) as $row)
            <x-skeleton @class(['h-3', ['w-full', 'w-11/12', 'w-4/5', 'w-2/3', 'w-3/4'][($row - 1) % 5]]) />
        @endforeach
    </div>
</div>
