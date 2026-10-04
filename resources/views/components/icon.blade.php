@props(['name'])

{{-- Lucide icon placeholder; resources/js/icons.js swaps it for an inline SVG. Defaults to size-4 when no class is given. --}}
<i data-lucide="{{ $name }}" aria-hidden="true" {{ $attributes->has('class') ? $attributes : $attributes->merge(['class' => 'size-4']) }}></i>
