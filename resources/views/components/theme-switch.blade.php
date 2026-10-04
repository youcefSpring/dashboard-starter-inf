{{-- Light / Dark / System picker. State lives in localStorage; resources/js/theme.js keeps aria-pressed in sync. --}}
<div {{ $attributes->merge(['class' => 'grid grid-cols-3 gap-1 rounded-lg bg-surface-2 p-1']) }} role="group" aria-label="{{ __('Theme') }}">
    @foreach (['light' => ['sun', __('Light')], 'dark' => ['moon', __('Dark')], 'system' => ['monitor', __('System')]] as $mode => [$icon, $label])
        <button type="button" data-set-theme="{{ $mode }}" aria-pressed="false"
            class="inline-flex items-center justify-center gap-1.5 rounded-md py-1.5 text-xs font-semibold text-fg-2 transition-colors hover:text-fg aria-pressed:bg-surface aria-pressed:text-fg aria-pressed:shadow-card">
            <x-icon :name="$icon" class="size-3.5" />{{ $label }}
        </button>
    @endforeach
</div>
