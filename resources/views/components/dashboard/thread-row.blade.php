@props(['name', 'tile' => null, 'message', 'time', 'unread' => 0, 'online' => false, 'mine' => false, 'href' => null])

{{-- Inbox conversation row. --}}
@php($tag = $href ? 'a' : 'button')

<{{ $tag }} @if ($href) href="{{ $href }}" @else type="button" @endif
    {{ $attributes->merge(['class' => 'flex w-full items-center gap-3 rounded-lg p-2 text-left transition-colors hover:bg-surface-2']) }}>
    <x-avatar :name="$name" :tile="$tile" :online="$online" />
    <span class="min-w-0 flex-1">
        <span class="flex items-center justify-between gap-2">
            <span @class(['truncate text-sm', 'font-bold' => $unread, 'font-semibold' => ! $unread])>{{ $name }}</span>
            <span @class(['shrink-0 text-xs', 'font-semibold text-primary-ink' => $unread, 'text-fg-3' => ! $unread])>{{ $time }}</span>
        </span>
        <span class="mt-0.5 flex items-center gap-2">
            <span @class(['truncate text-[13px]', 'text-fg' => $unread, 'text-fg-2' => ! $unread])>@if ($mine)<span class="text-fg-3">{{ __('You:') }} </span>@endif{{ $message }}</span>
            @if ($unread)
                <span class="num ml-auto grid h-5 min-w-5 shrink-0 place-items-center rounded-full bg-secondary px-1.5 text-[11px] font-bold text-bg">{{ $unread }}<span class="sr-only"> {{ __('unread') }}</span></span>
            @endif
        </span>
    </span>
</{{ $tag }}>
