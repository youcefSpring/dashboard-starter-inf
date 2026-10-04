@props(['client' => false])

{{--
    Wraps a table and its toolbar so selection, bulk actions and (with `client`) in-browser
    search, filtering and sorting work together. See resources/js/table.js.
--}}
<div data-table-root @if ($client) data-client @endif {{ $attributes->merge(['class' => 'space-y-3']) }}>
    {{ $slot }}
</div>
