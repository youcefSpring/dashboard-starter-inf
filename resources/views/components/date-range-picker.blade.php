@props([
    'presets' => [],
    'selected' => null,
    'from' => null,
    'to' => null,
    'action' => null,
    'presetParam' => 'period',
    'align' => 'end',
    'compact' => false,
])

{{--
    Date range dropdown that submits as a GET request: presets become ?{presetParam}=key links,
    the custom range submits ?from=Y-m-d&to=Y-m-d. Pass `presets` as [key => label].
--}}
@php
    $action ??= url()->current();
    $isCustom = $from && $to;
    $title = $isCustom ? __('Custom range') : ($presets[$selected] ?? __('Choose dates'));
    $dates = $isCustom
        ? \Illuminate\Support\Carbon::parse($from)->format('M j').' – '.\Illuminate\Support\Carbon::parse($to)->format('M j, Y')
        : null;
@endphp

<x-dropdown :align="$align" width="w-[min(300px,calc(100vw-2rem))]" :label="__('Choose a date range')" {{ $attributes }}>
    <x-slot:trigger>
        <button type="button" data-dropdown-trigger aria-haspopup="menu" aria-expanded="false"
            class="inline-flex h-10 items-center gap-2 whitespace-nowrap rounded-lg border border-line bg-surface px-3 text-sm shadow-card transition-colors hover:border-line-strong hover:bg-surface-2">
            <x-icon name="calendar" class="size-4 text-fg-3" />
            <span class="font-semibold">{{ $title }}</span>
            @if ($dates && ! $compact)
                <span class="num hidden text-fg-2 lg:inline">{{ $dates }}</span>
            @endif
            <x-icon name="chevron-down" class="size-4 text-fg-3" />
        </button>
    </x-slot:trigger>

    <div class="p-1.5">
        <x-dropdown-header>{{ __('Presets') }}</x-dropdown-header>
        @foreach ($presets as $key => $label)
            <a href="{{ $action }}?{{ http_build_query([$presetParam => $key]) }}" role="menuitem"
                @class([
                    'flex items-center justify-between rounded-lg px-2.5 py-2 text-sm font-medium transition-colors',
                    'bg-primary-soft text-primary-ink' => ! $isCustom && (string) $selected === (string) $key,
                    'text-fg hover:bg-surface-2' => $isCustom || (string) $selected !== (string) $key,
                ])>
                {{ $label }}
                @if (! $isCustom && (string) $selected === (string) $key)<x-icon name="check" />@endif
            </a>
        @endforeach
    </div>

    <form method="GET" action="{{ $action }}" class="space-y-3 border-t border-line p-3">
        <p class="text-xs font-semibold text-fg-3">{{ __('Custom range') }}</p>
        <div class="grid grid-cols-2 gap-2">
            <label class="text-xs font-medium text-fg-2">{{ __('From') }}
                <input type="date" name="from" value="{{ $from }}" max="{{ now()->toDateString() }}" required class="form-control num mt-1 h-9 px-2" />
            </label>
            <label class="text-xs font-medium text-fg-2">{{ __('To') }}
                <input type="date" name="to" value="{{ $to }}" max="{{ now()->toDateString() }}" required class="form-control num mt-1 h-9 px-2" />
            </label>
        </div>
        <x-button type="submit" variant="primary" size="sm" class="w-full">{{ __('Apply') }}</x-button>
    </form>
</x-dropdown>
