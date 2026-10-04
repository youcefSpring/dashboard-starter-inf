@props(['icon' => 'cloud-off', 'title', 'text' => null])

<div role="alert" {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center rounded-xl border border-dashed border-error/35 bg-error/5 px-6 py-10 text-center']) }}>
    <span class="grid size-12 place-items-center rounded-xl bg-error/10 text-error"><x-icon :name="$icon" class="size-5" /></span>
    <p class="mt-4 text-sm font-semibold text-fg">{{ $title }}</p>
    @if ($text)
        <p class="mt-1 max-w-md text-[13px] text-fg-2">{{ $text }}</p>
    @endif
    @if ($slot->isNotEmpty())
        <div class="mt-4 flex flex-wrap justify-center gap-2">{{ $slot }}</div>
    @endif
</div>
