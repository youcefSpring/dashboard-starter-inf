@props(['icon', 'tone' => 'primary', 'title', 'time' => null, 'unread' => false])

<div {{ $attributes->class(['flex gap-3 border-b border-line px-4 py-3 transition-colors last:border-b-0', 'bg-primary-soft/40' => $unread]) }}>
    <x-icon-tile :icon="$icon" :tone="$tone" />
    <div class="min-w-0 flex-1">
        <p class="text-sm font-semibold">{{ $title }}</p>
        @if ($slot->isNotEmpty())
            <p class="mt-0.5 text-[13px] text-fg-2">{{ $slot }}</p>
        @endif
        @if ($time)
            <p class="mt-1 text-xs text-fg-3">{{ $time }}</p>
        @endif
        @isset($actions)
            <div class="mt-2.5 flex gap-2">{{ $actions }}</div>
        @endisset
    </div>
    @if ($unread)
        <span class="mt-1.5 size-2 shrink-0 rounded-full bg-secondary"><span class="sr-only">{{ __('Unread') }}</span></span>
    @endif
</div>
