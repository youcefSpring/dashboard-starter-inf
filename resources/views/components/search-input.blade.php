@props(['name' => 'q', 'value' => null, 'placeholder' => null, 'size' => 'md'])

<div class="relative {{ $attributes->get('class') }}">
    <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-fg-3"><x-icon name="search" /></span>
    <input type="search" name="{{ $name }}" value="{{ $value }}" placeholder="{{ $placeholder ?? __('Search') }}" autocomplete="off"
        aria-label="{{ $attributes->get('aria-label', $placeholder ?? __('Search')) }}"
        {{ $attributes->except(['class', 'aria-label'])->class(['form-control pl-9 pr-3 shadow-none', 'h-9' => $size === 'md', 'h-10' => $size === 'lg']) }} />
</div>
