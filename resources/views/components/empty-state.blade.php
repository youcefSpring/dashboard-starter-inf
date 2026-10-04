@props(['icon' => 'inbox', 'title', 'text' => null])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center px-6 py-10 text-center']) }}>
    <span class="grid size-12 place-items-center rounded-xl bg-primary-soft text-primary-ink"><x-icon :name="$icon" class="size-5" /></span>
    <p class="mt-4 text-sm font-semibold text-fg">{{ $title }}</p>
    @if ($text)
        <p class="mt-1 max-w-sm text-[13px] text-fg-2">{{ $text }}</p>
    @endif
    @if ($slot->isNotEmpty())
        <div class="mt-4 flex gap-2">{{ $slot }}</div>
    @endif
</div>
