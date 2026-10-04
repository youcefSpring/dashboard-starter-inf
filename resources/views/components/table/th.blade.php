@props(['align' => 'left', 'column' => null])

@aware(['sort' => null, 'direction' => null, 'client' => false])

{{--
    Header cell. `column="name"` makes it sortable: a link that toggles ?sort=name&direction=…
    (server tables; pass :sort and :direction to x-table), or a button that sorts rows in place
    (client tables; rows need data-sort-name="…").
--}}
@php
    $activeDirection = $column && ! $client && $sort === $column ? $direction : null;
    $href = null;

    if ($column && ! $client) {
        $href = request()->fullUrlWithQuery([
            'sort' => $column,
            'direction' => $sort === $column && $direction === 'asc' ? 'desc' : 'asc',
            'page' => null,
        ]);
    }

    $linkClasses = 'group inline-flex items-center gap-1 rounded-md transition-colors hover:text-fg'
        .($align === 'right' ? ' flex-row-reverse' : '')
        .($activeDirection ? ' text-fg' : '');
@endphp

<th scope="col" {{ $attributes->class([
    'whitespace-nowrap px-2.5 py-2.5 font-semibold first:pl-4 last:pr-4 sm:first:pl-5 sm:last:pr-5',
    'text-right' => $align === 'right',
]) }} @if ($activeDirection) aria-sort="{{ $activeDirection === 'asc' ? 'ascending' : 'descending' }}" @endif>
    @if ($column && $client)
        <button type="button" data-sort-key="{{ $column }}" class="{{ $linkClasses }}">
            {{ $slot }}
            <x-icon name="arrow-up" class="hidden size-3.5 text-primary-ink" data-sort-icon="asc" />
            <x-icon name="arrow-down" class="hidden size-3.5 text-primary-ink" data-sort-icon="desc" />
            <x-icon name="chevrons-up-down" class="size-3.5 opacity-0 transition-opacity group-hover:opacity-60" data-sort-icon="none" />
        </button>
    @elseif ($href)
        <a href="{{ $href }}" class="{{ $linkClasses }}">
            {{ $slot }}
            @if ($activeDirection)
                <x-icon :name="$activeDirection === 'asc' ? 'arrow-up' : 'arrow-down'" class="size-3.5 text-primary-ink" />
            @else
                <x-icon name="chevrons-up-down" class="size-3.5 opacity-0 transition-opacity group-hover:opacity-60" />
            @endif
        </a>
    @else
        {{ $slot }}
    @endif
</th>
