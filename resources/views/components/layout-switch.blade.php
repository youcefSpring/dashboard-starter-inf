{{-- Horizontal (top navigation) / Vertical (sidebar) picker. Stored in localStorage; see resources/js/layout.js. --}}
<div {{ $attributes->merge(['class' => 'grid grid-cols-2 gap-1 rounded-lg bg-surface-2 p-1']) }} role="group" aria-label="{{ __('Layout') }}">
    @foreach (['horizontal' => ['panel-top', __('Top nav')], 'vertical' => ['panel-left', __('Sidebar')]] as $layout => [$icon, $label])
        <button type="button" data-set-layout="{{ $layout }}" aria-pressed="false"
            class="inline-flex items-center justify-center gap-1.5 rounded-md py-1.5 text-xs font-semibold text-fg-2 transition-colors hover:text-fg aria-pressed:bg-surface aria-pressed:text-fg aria-pressed:shadow-card">
            <x-icon :name="$icon" class="size-3.5" />{{ $label }}
        </button>
    @endforeach
</div>
