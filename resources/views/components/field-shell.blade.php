@props(['for', 'label', 'error' => null, 'hint' => null, 'optional' => false])

{{-- Label, hint and error wrapper shared by x-field, x-select and x-textarea. Put the control in the slot. --}}
<div {{ $attributes }}>
    <div class="mb-1.5 flex items-baseline justify-between gap-3">
        <label for="{{ $for }}" class="text-[13px] font-semibold text-fg">{{ $label }}</label>
        @if ($optional)
            <span class="text-xs text-fg-3">{{ __('Optional') }}</span>
        @endif
        {{ $aside ?? '' }}
    </div>

    {{ $slot }}

    @if ($error)
        <p id="{{ $for }}-error" class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-error">
            <x-icon name="circle-alert" class="size-3.5" />{{ $error }}
        </p>
    @elseif ($hint)
        <p id="{{ $for }}-hint" class="mt-1.5 text-xs text-fg-3">{{ $hint }}</p>
    @endif
</div>
