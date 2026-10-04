@props(['name', 'label', 'options', 'counts' => [], 'selected' => null, 'client' => false])

{{--
    Filter dropdown for a table.
    Server tables: single choice, each option is a link setting ?{name}=value.
    Client tables (`client`): multi-select toggles matching rows' data-filter-{name} attribute.
--}}
@php($active = $client ? false : filled($selected))

<x-dropdown align="start" width="w-60" :label="$label" floating>
    <x-slot:trigger>
        <x-filter-chip :count="$active ? 1 : 0" data-dropdown-trigger aria-haspopup="menu" aria-expanded="false" :data-table-filter-chip="$client ? $name : null">{{ $label }}</x-filter-chip>
    </x-slot:trigger>

    <div class="p-1.5">
        <x-dropdown-header>{{ __('Filter by :label', ['label' => mb_strtolower($label)]) }}</x-dropdown-header>
        @foreach ($options as $value => $optionLabel)
            @if ($client)
                <button type="button" role="menuitemcheckbox" aria-checked="false" data-table-filter="{{ $name }}" data-value="{{ $value }}"
                    class="group flex w-full items-center gap-2.5 rounded-lg px-2.5 py-2 text-left text-sm transition-colors hover:bg-surface-2">
                    <span class="grid size-4 shrink-0 place-items-center rounded border border-line-strong bg-surface text-primary-on group-aria-checked:border-primary group-aria-checked:bg-primary" aria-hidden="true">
                        <x-icon name="check" class="hidden size-3 group-aria-checked:block" stroke-width="3" />
                    </span>
                    <span class="flex-1">{{ $optionLabel }}</span>
                    @isset($counts[$value])<span class="num text-xs text-fg-3">{{ $counts[$value] }}</span>@endisset
                </button>
            @else
                @php($on = (string) $selected === (string) $value)
                <a href="{{ request()->fullUrlWithQuery([$name => $on ? null : $value, 'page' => null]) }}" role="menuitemradio" aria-checked="{{ $on ? 'true' : 'false' }}"
                    class="flex items-center gap-2.5 rounded-lg px-2.5 py-2 text-sm font-medium transition-colors hover:bg-surface-2">
                    <span @class(['grid size-4 shrink-0 place-items-center rounded-full', 'bg-primary text-primary-on' => $on, 'border border-line-strong bg-surface' => ! $on]) aria-hidden="true">
                        @if ($on)<span class="size-1.5 rounded-full bg-primary-on"></span>@endif
                    </span>
                    <span class="flex-1">{{ $optionLabel }}</span>
                    @isset($counts[$value])<span class="num text-xs text-fg-3">{{ $counts[$value] }}</span>@endisset
                </a>
            @endif
        @endforeach
    </div>
    @if ($client)
        <div class="border-t border-line p-2">
            <x-button size="sm" variant="ghost" class="w-full" data-table-filter-clear="{{ $name }}">{{ __('Clear') }}</x-button>
        </div>
    @elseif ($active)
        <div class="border-t border-line p-2">
            <x-button size="sm" variant="ghost" class="w-full" :href="request()->fullUrlWithQuery([$name => null, 'page' => null])">{{ __('Clear') }}</x-button>
        </div>
    @endif
</x-dropdown>
