@props(['id', 'title', 'description' => null, 'icon' => null, 'tone' => 'primary', 'size' => 'sm:max-w-lg'])

{{--
    Native <dialog> modal: a bottom sheet on phones, centred from `sm` up.
    Open with any element carrying data-dialog-open="{id}"; close with data-dialog-close, Escape or the backdrop.
--}}
<dialog id="{{ $id }}" aria-labelledby="{{ $id }}-title" @if ($description) aria-describedby="{{ $id }}-description" @endif
    {{ $attributes->class([
        'modal m-0 mt-auto max-h-[92vh] w-full max-w-full rounded-t-2xl border border-line bg-surface p-0 text-fg shadow-pop',
        'sm:m-auto sm:w-[calc(100%-3rem)] sm:rounded-2xl',
        $size,
    ]) }}>
    <div class="flex max-h-[92vh] flex-col">
        <div class="mx-auto mt-2 h-1 w-10 rounded-full bg-line-strong sm:hidden" aria-hidden="true"></div>
        <div class="flex items-start gap-3 px-5 pb-3 pt-4 sm:pt-5">
            @if ($icon)
                <x-icon-tile :icon="$icon" :tone="$tone" size="lg" />
            @endif
            <div class="min-w-0 flex-1 pt-0.5">
                <h2 id="{{ $id }}-title" class="text-base font-semibold">{{ $title }}</h2>
                @if ($description)
                    <p id="{{ $id }}-description" class="mt-1 text-[13px] text-fg-2">{{ $description }}</p>
                @endif
            </div>
            <x-icon-button icon="x" :label="__('Close')" size="sm" class="-mr-2 -mt-1" data-dialog-close />
        </div>

        @if ($slot->isNotEmpty())
            <div class="flex-1 overflow-y-auto px-5 pb-4 pt-1 scroll-thin">{{ $slot }}</div>
        @endif

        @isset($footer)
            <div class="flex flex-col-reverse gap-2 border-t border-line px-5 py-4 sm:flex-row sm:justify-end">{{ $footer }}</div>
        @endisset
    </div>
</dialog>
