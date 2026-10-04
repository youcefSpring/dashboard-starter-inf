@props(['name', 'label', 'value' => null, 'rows' => 4, 'hint' => null, 'optional' => false, 'bag' => 'default'])

@php
    $id = $attributes->get('id', $name);
    $error = $errors->getBag($bag)->first($name);
    // Old input belongs to the form that failed: the default bag, or this field's bag when it has errors.
    $useOld = $bag === 'default' || $errors->getBag($bag)->isNotEmpty();
@endphp

<x-field-shell :for="$id" :label="$label" :error="$error" :hint="$hint" :optional="$optional">
    <textarea id="{{ $id }}" name="{{ $name }}" rows="{{ $rows }}"
        @if ($error) aria-invalid="true" aria-describedby="{{ $id }}-error" @elseif ($hint) aria-describedby="{{ $id }}-hint" @endif
        {{ $attributes->except('id')->class('form-control px-3 py-2.5') }}>{{ $useOld ? old($name, $value) : $value }}</textarea>
</x-field-shell>
