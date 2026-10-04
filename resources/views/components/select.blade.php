@props(['name', 'label', 'options' => [], 'value' => null, 'placeholder' => null, 'hint' => null, 'optional' => false, 'bag' => 'default'])

{{-- Native select. `options` is [value => label]; pass <option>s in the slot instead for full control. --}}
@php
    $id = $attributes->get('id', $name);
    $error = $errors->getBag($bag)->first($name);
    // Old input belongs to the form that failed: the default bag, or this field's bag when it has errors.
    $useOld = $bag === 'default' || $errors->getBag($bag)->isNotEmpty();
    $selected = (string) ($useOld ? old($name, $value) : $value);
@endphp

<x-field-shell :for="$id" :label="$label" :error="$error" :hint="$hint" :optional="$optional">
    <div class="relative">
        <select id="{{ $id }}" name="{{ $name }}"
            @if ($error) aria-invalid="true" aria-describedby="{{ $id }}-error" @elseif ($hint) aria-describedby="{{ $id }}-hint" @endif
            {{ $attributes->except('id')->class('form-control h-10 appearance-none pl-3 pr-9') }}>
            @if ($placeholder)
                <option value="" disabled @selected($selected === '')>{{ $placeholder }}</option>
            @endif
            @foreach ($options as $optionValue => $optionLabel)
                <option value="{{ $optionValue }}" @selected($selected === (string) $optionValue)>{{ $optionLabel }}</option>
            @endforeach
            {{ $slot }}
        </select>
        <x-icon name="chevron-down" class="pointer-events-none absolute right-3 top-1/2 size-4 -translate-y-1/2 text-fg-3" />
    </div>
</x-field-shell>
