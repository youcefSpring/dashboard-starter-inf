@props(['icon', 'tone' => 'primary', 'size' => 'md', 'round' => false])

{{-- Tinted icon square used by notifications, timelines and modals. --}}
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
    $sizes = ['sm' => ['size-7', 'size-3.5'], 'md' => ['size-9', 'size-4'], 'lg' => ['size-11', 'size-5']];
@endphp

<span {{ $attributes->class([
    'relative grid shrink-0 place-items-center ring-1 ring-inset',
    $sizes[$size][0],
    $tones[$tone],
    'rounded-full' => $round,
    'rounded-lg' => ! $round && $size !== 'lg',
    'rounded-xl' => ! $round && $size === 'lg',
]) }}><x-icon :name="$icon" :class="$sizes[$size][1]" /></span>
