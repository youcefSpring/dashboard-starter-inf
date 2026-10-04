@props(['id', 'title', 'subtitle' => null, 'width' => 'sm:max-w-md'])

{{-- Right-hand side panel on a native <dialog>; full screen on phones. Opens with data-dialog-open="{id}". --}}
<dialog id="{{ $id }}" aria-labelledby="{{ $id }}-title"
    {{ $attributes->class(['drawer m-0 ml-auto h-dvh max-h-none w-full max-w-full border-l border-line bg-surface p-0 text-fg shadow-pop', $width]) }}>
    <div class="flex h-full flex-col">
        <header class="flex min-h-16 items-center gap-2 border-b border-line px-4 py-3 sm:px-5">
            <div class="min-w-0 flex-1">
                <h2 id="{{ $id }}-title" class="truncate text-[15px] font-semibold">{{ $title }}</h2>
                @if ($subtitle)
                    <p class="truncate text-xs text-fg-3">{{ $subtitle }}</p>
                @endif
            </div>
            {{ $headerActions ?? '' }}
            <x-icon-button icon="x" :label="__('Close panel')" size="sm" data-dialog-close />
        </header>

        <div class="flex-1 overflow-y-auto scroll-thin">{{ $slot }}</div>

        @isset($footer)
            <footer class="flex gap-2 border-t border-line px-4 py-3 sm:px-5">{{ $footer }}</footer>
        @endisset
    </div>
</dialog>
