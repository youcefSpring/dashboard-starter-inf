@props(['mine' => false])

<div {{ $attributes->class([
    'max-w-[82%] whitespace-pre-wrap break-words rounded-2xl px-3.5 py-2 text-sm',
    'self-end rounded-br-md bg-primary text-primary-on' => $mine,
    'self-start rounded-bl-md bg-surface-2 text-fg' => ! $mine,
]) }}>{{ $slot }}</div>
