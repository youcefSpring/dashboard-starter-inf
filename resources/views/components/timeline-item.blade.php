@props(['icon', 'tone' => 'primary', 'time' => null])

<li {{ $attributes->merge(['class' => 'group relative flex gap-3 pb-5 last:pb-0']) }}>
    <span class="absolute -bottom-0 left-[17px] top-10 w-px bg-line group-last:hidden" aria-hidden="true"></span>
    <x-icon-tile :icon="$icon" :tone="$tone" round />
    <div class="min-w-0 flex-1 pt-0.5">
        <p class="text-[13px] leading-5 text-fg-2 [&_b]:font-semibold [&_b]:text-fg">{{ $slot }}</p>
        @if ($time || isset($meta))
            <div class="mt-1.5 flex flex-wrap items-center gap-2">
                @if ($time)<time class="text-xs text-fg-3">{{ $time }}</time>@endif
                {{ $meta ?? '' }}
            </div>
        @endif
    </div>
</li>
