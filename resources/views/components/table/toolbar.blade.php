{{--
    Row under the table filters. The default slot is the summary (result count, active filter chips);
    the `bulk` slot replaces it while rows are selected and holds bulk actions.
--}}
<div {{ $attributes->merge(['class' => 'flex min-h-10 flex-wrap items-center gap-2']) }}>
    <div data-table-summary class="flex flex-wrap items-center gap-2 text-[13px] text-fg-2">{{ $slot }}</div>

    @isset($bulk)
        <div data-table-bulk hidden role="region" aria-label="{{ __('Bulk actions') }}"
            class="w-full items-center gap-1 rounded-lg bg-primary-soft px-2 py-1 ring-1 ring-inset ring-primary/15 [&:not([hidden])]:flex [&:not([hidden])]:flex-wrap">
            <span class="num px-1.5 text-sm font-semibold text-primary-ink"><span data-table-selected-count>0</span> {{ __('selected') }}</span>
            <span class="mx-1 hidden h-5 w-px bg-primary/20 sm:block"></span>
            {{ $bulk }}
            <button type="button" data-table-clear class="ml-auto rounded-md px-2 py-1 text-[13px] font-semibold text-primary-ink hover:underline">{{ __('Clear selection') }}</button>
        </div>
    @endisset
</div>
