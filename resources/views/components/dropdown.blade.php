@props(['align' => 'end', 'position' => 'bottom', 'width' => 'w-72', 'label' => null, 'floating' => false])

{{--
    Accessible dropdown. The trigger slot must contain one element with [data-dropdown-trigger];
    resources/js/dropdown.js handles toggling, outside clicks, Escape and arrow keys.
    Use `floating` inside scrolling or clipped containers (tables): the panel is then positioned
    against the viewport and flips upward when there is no room below.
--}}
<div data-dropdown @if ($floating) data-dropdown-floating data-align="{{ $align }}" @endif {{ $attributes->merge(['class' => 'relative']) }}>
    {{ $trigger }}

    <div data-dropdown-panel role="menu" @if ($label) aria-label="{{ $label }}" @endif hidden
        @class([
            'pop-in absolute z-[70] max-h-[calc(100vh-24px)] overflow-auto rounded-xl border border-line bg-surface shadow-pop scroll-thin',
            $width,
            'right-0' => $align === 'end',
            'left-0' => $align === 'start',
            'top-full mt-2' => $position === 'bottom',
            'bottom-full mb-2' => $position === 'top',
        ])>
        {{ $slot }}
    </div>
</div>
