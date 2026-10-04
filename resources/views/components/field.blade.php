@props([
    'name',
    'label',
    'type' => 'text',
    'value' => null,
    'icon' => null,
    'prefix' => null,
    'hint' => null,
    'optional' => false,
    'bag' => 'default',
])

@php
    $id = $attributes->get('id', $name);
    $error = $errors->getBag($bag)->first($name);
    // Old input belongs to the form that failed: the default bag, or this field's bag when it has errors.
    $useOld = $bag === 'default' || $errors->getBag($bag)->isNotEmpty();
    $describedBy = $error ? $id.'-error' : ($hint ? $id.'-hint' : null);
    $isPassword = $type === 'password';
@endphp

<x-field-shell :for="$id" :label="$label" :error="$error" :hint="$hint" :optional="$optional">
    @isset($aside)
        <x-slot:aside>{{ $aside }}</x-slot:aside>
    @endisset

    <div class="relative">
        @if ($icon)
            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-fg-3"><x-icon :name="$icon" /></span>
        @elseif ($prefix)
            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-fg-3">{{ $prefix }}</span>
        @endif

        <input
            id="{{ $id }}"
            name="{{ $name }}"
            type="{{ $type }}"
            @unless ($isPassword) value="{{ $useOld ? old($name, $value) : $value }}" @endunless
            @if ($error) aria-invalid="true" @endif
            @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
            {{ $attributes->except('id')->class([
                'form-control h-10',
                'pl-9' => $icon,
                'pl-7' => ! $icon && $prefix,
                'pl-3' => ! $icon && ! $prefix,
                'pr-10' => $isPassword,
                'pr-3' => ! $isPassword,
            ]) }}
        />

        @if ($isPassword)
            <button type="button" data-password-toggle="{{ $id }}" aria-label="{{ __('Show password') }}" aria-pressed="false"
                class="absolute right-1 top-1/2 grid size-8 -translate-y-1/2 place-items-center rounded-md text-fg-3 transition-colors hover:bg-surface-2 hover:text-fg">
                <x-icon name="eye" data-show />
                <x-icon name="eye-off" class="hidden size-4" data-hide />
            </button>
        @endif
    </div>
</x-field-shell>
