@props(['title', 'description' => null])

<div {{ $attributes->merge(['class' => 'flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between']) }}>
    <div class="min-w-0">
        <h1 class="text-2xl font-bold tracking-tight sm:text-[28px] sm:leading-9">{{ $title }}</h1>
        @if ($description)
            <p class="mt-1 text-[15px] text-fg-2">{{ $description }}</p>
        @endif
        {{ $meta ?? '' }}
    </div>
    @isset($actions)
        <div class="flex shrink-0 flex-wrap items-center gap-2">{{ $actions }}</div>
    @endisset
</div>
