@props(['name', 'label', 'checked' => false])

<label class="inline-flex cursor-pointer select-none items-center gap-2.5 text-[13px] font-medium text-fg-2">
    <input type="checkbox" name="{{ $name }}" value="1" @checked(old($name, $checked))
        {{ $attributes->merge(['class' => 'size-4 cursor-pointer rounded border-line accent-primary']) }} />
    {{ $label }}
</label>
