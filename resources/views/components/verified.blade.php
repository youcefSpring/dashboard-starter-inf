@props(['size' => 'size-4'])

<span {{ $attributes->merge(['class' => 'inline-flex text-primary-ink']) }} title="{{ __('Verified creator') }}">
    <x-icon name="badge-check" :class="$size" stroke-width="2" /><span class="sr-only">{{ __('Verified') }}</span>
</span>
