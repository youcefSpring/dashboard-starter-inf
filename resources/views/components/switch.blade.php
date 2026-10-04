@props(['name', 'label', 'description' => null, 'checked' => false])

{{-- Toggle switch backed by a real checkbox, so it submits with forms and works without JavaScript. --}}
@php($id = $attributes->get('id', $name))

<label for="{{ $id }}" class="flex cursor-pointer items-center gap-4 has-disabled:cursor-not-allowed has-disabled:opacity-60">
    <span class="min-w-0 flex-1">
        <span class="block text-sm font-semibold text-fg">{{ $label }}</span>
        @if ($description)
            <span class="mt-0.5 block text-xs text-fg-2">{{ $description }}</span>
        @endif
    </span>
    <span class="relative inline-flex shrink-0">
        <input type="hidden" name="{{ $name }}" value="0" />
        <input id="{{ $id }}" type="checkbox" role="switch" name="{{ $name }}" value="1" @checked(old($name, $checked))
            {{ $attributes->except('id')->class('peer sr-only') }} />
        <span class="h-6 w-10 rounded-full bg-surface-3 ring-1 ring-inset ring-line-strong transition-colors peer-checked:bg-primary peer-checked:ring-primary peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-primary"></span>
        <span class="absolute left-0.5 top-0.5 size-5 rounded-full bg-white shadow transition-transform peer-checked:translate-x-4"></span>
    </span>
</label>
