@props(['label' => __('Row actions'), 'width' => 'w-52'])

{{-- Last-column "⋯" menu. Put x-dropdown-item / x-dropdown-separator in the slot. --}}
<td class="w-12 text-right">
    <x-dropdown :width="$width" :label="$label" floating class="inline-block">
        <x-slot:trigger>
            <x-icon-button icon="ellipsis" :label="$label" size="sm" data-dropdown-trigger aria-haspopup="menu" aria-expanded="false" />
        </x-slot:trigger>
        <div class="p-1.5 text-left">{{ $slot }}</div>
    </x-dropdown>
</td>
