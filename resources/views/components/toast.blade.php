@props(['type' => 'success', 'title', 'text' => null])

{{-- One toast. Server flashes render through x-toasts; from JavaScript call window.toast({ type, title, text }). --}}
@php
    [$icon, $color] = [
        'success' => ['circle-check', 'text-success'],
        'error' => ['circle-alert', 'text-error'],
        'warning' => ['triangle-alert', 'text-warning'],
        'info' => ['info', 'text-info'],
    ][$type];
@endphp

<div data-toast role="{{ $type === 'error' ? 'alert' : 'status' }}"
    {{ $attributes->merge(['class' => 'toast-in pointer-events-auto flex w-full max-w-sm items-start gap-3 rounded-xl border border-line bg-surface p-3.5 shadow-pop sm:w-[380px]']) }}>
    <span class="mt-px {{ $color }}"><x-icon :name="$icon" class="size-5" /></span>
    <div class="min-w-0 flex-1">
        <p class="text-sm font-semibold">{{ $title }}</p>
        @if ($text)
            <p class="mt-0.5 text-[13px] text-fg-2">{{ $text }}</p>
        @endif
    </div>
    <button type="button" data-toast-close aria-label="{{ __('Dismiss') }}" class="-mr-1 -mt-1 grid size-7 shrink-0 place-items-center rounded-md text-fg-3 transition-colors hover:bg-surface-2 hover:text-fg">
        <x-icon name="x" />
    </button>
</div>
