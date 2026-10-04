@props(['title' => null, 'subtitle' => null, 'bodyClass' => 'px-4 pb-4 pt-4 sm:px-5 sm:pb-5'])

<section {{ $attributes->merge(['class' => 'flex flex-col rounded-xl border border-line bg-surface shadow-card']) }}>
    @if ($title || isset($actions))
        <header class="flex flex-wrap items-start justify-between gap-x-4 gap-y-3 px-4 pt-4 sm:px-5 sm:pt-5">
            <div class="min-w-0">
                @if ($title)
                    <h2 class="text-[15px] font-semibold leading-6 text-fg">{{ $title }}</h2>
                @endif
                @if ($subtitle)
                    <p class="mt-0.5 text-[13px] text-fg-2">{{ $subtitle }}</p>
                @endif
            </div>
            @isset($actions)
                <div class="flex min-w-0 max-w-full flex-wrap items-center gap-2">{{ $actions }}</div>
            @endisset
        </header>
    @endif

    <div class="flex-1 {{ $bodyClass }}">{{ $slot }}</div>

    @isset($footer)
        <footer class="border-t border-line px-4 py-3 sm:px-5">{{ $footer }}</footer>
    @endisset
</section>
