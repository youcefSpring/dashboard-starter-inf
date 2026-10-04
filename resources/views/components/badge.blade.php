@props(['tone' => 'neutral', 'dot' => false, 'icon' => null])

@php
    $tones = [
        'success' => 'bg-success/10 text-success ring-success/20',
        'warning' => 'bg-warning/10 text-warning ring-warning/25',
        'error' => 'bg-error/10 text-error ring-error/20',
        'info' => 'bg-info/10 text-info ring-info/20',
        'primary' => 'bg-primary/10 text-primary-ink ring-primary/20',
        'secondary' => 'bg-secondary/10 text-secondary ring-secondary/20',
        'neutral' => 'bg-surface-2 text-fg-2 ring-line',
    ];

    $dots = [
        'success' => 'bg-success',
        'warning' => 'bg-warning',
        'error' => 'bg-error',
        'info' => 'bg-info',
        'primary' => 'bg-primary',
        'secondary' => 'bg-secondary',
        'neutral' => 'bg-fg-3',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 whitespace-nowrap rounded-md px-2 py-0.5 text-xs font-semibold ring-1 ring-inset '.$tones[$tone]]) }}>
    @if ($dot)<span class="size-1.5 rounded-full {{ $dots[$tone] }}"></span>@endif
    @if ($icon)<x-icon :name="$icon" class="size-3.5" />@endif
    {{ $slot }}
</span>
