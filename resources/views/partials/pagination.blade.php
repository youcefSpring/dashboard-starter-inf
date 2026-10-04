@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator $paginator */
    $navButton = 'grid size-8 place-items-center rounded-lg border border-line bg-surface text-fg-2 transition-colors hover:bg-surface-2 hover:text-fg';
    $disabledButton = 'grid size-8 place-items-center rounded-lg border border-line bg-surface text-fg-2 opacity-40';
@endphp

<nav class="flex flex-col items-center justify-between gap-3 sm:flex-row" aria-label="{{ __('Pagination') }}">
    <p class="text-[13px] text-fg-2">
        {{ __('Showing') }}
        <span class="num font-semibold text-fg">{{ $paginator->firstItem() ?? 0 }}–{{ $paginator->lastItem() ?? 0 }}</span>
        {{ __('of') }}
        <span class="num font-semibold text-fg">{{ $paginator->total() }}</span>
        {{ __('results') }}
    </p>

    @if ($paginator->hasPages())
        <div class="flex items-center gap-1">
            @if ($paginator->onFirstPage())
                <span class="{{ $disabledButton }}" aria-disabled="true" aria-label="{{ __('Previous page') }}"><x-icon name="chevron-left" class="size-4" /></span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="{{ $navButton }}" aria-label="{{ __('Previous page') }}"><x-icon name="chevron-left" class="size-4" /></a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-1 text-fg-3">…</span>
                @else
                    @foreach ($element as $page => $url)
                        @if ($page === $paginator->currentPage())
                            <span aria-current="page" class="num grid h-8 min-w-8 place-items-center rounded-lg bg-primary px-2 text-[13px] font-semibold text-primary-on">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="num grid h-8 min-w-8 place-items-center rounded-lg px-2 text-[13px] font-semibold text-fg-2 transition-colors hover:bg-surface-2 hover:text-fg">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="{{ $navButton }}" aria-label="{{ __('Next page') }}"><x-icon name="chevron-right" class="size-4" /></a>
            @else
                <span class="{{ $disabledButton }}" aria-disabled="true" aria-label="{{ __('Next page') }}"><x-icon name="chevron-right" class="size-4" /></span>
            @endif
        </div>
    @endif
</nav>
