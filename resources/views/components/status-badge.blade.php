@props(['status'])

@php
    $tone = [
        'Active' => 'success',
        'Draft' => 'neutral',
        'Pending approval' => 'info',
        'Completed' => 'primary',
        'Paused' => 'warning',
    ][$status] ?? 'neutral';
@endphp

<x-badge :tone="$tone" dot {{ $attributes }}>{{ $status }}</x-badge>
