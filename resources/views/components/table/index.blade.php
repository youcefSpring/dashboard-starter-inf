@props(['minWidth' => '640px', 'sort' => null, 'direction' => null, 'client' => false])

{{--
    Edge-to-edge scrolling table for use inside x-card.
    Header cells go in the `head` slot (x-table.th, x-table.select-all); rows in the default slot.
    Pass the active `sort`/`direction` so sortable headers can mark themselves, and `client`
    when sorting happens in the browser (see x-table.root).
--}}
<div class="relative -mx-4 overflow-x-auto scroll-thin sm:-mx-5">
    <table {{ $attributes->merge(['class' => 'w-full text-left text-[13px]']) }} style="min-width: {{ $minWidth }}">
        <thead>
            <tr class="bg-surface-2/70 text-xs font-semibold text-fg-2">{{ $head }}</tr>
        </thead>
        <tbody data-table-body class="[&>tr]:border-t [&>tr]:border-line [&>tr]:transition-colors [&>tr:hover]:bg-surface-2/60 [&>tr[data-selected]]:bg-primary-soft/60 [&_td]:px-2.5 [&_td]:py-3 [&_td]:align-middle [&_td:first-child]:pl-4 [&_td:last-child]:pr-4 sm:[&_td:first-child]:pl-5 sm:[&_td:last-child]:pr-5">
            {{ $slot }}
        </tbody>
    </table>
</div>
