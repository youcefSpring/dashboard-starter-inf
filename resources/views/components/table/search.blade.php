@props(['name' => 'q', 'value' => null, 'placeholder' => null, 'client' => false])

{{--
    Table search box.
    Server tables: a GET form that keeps the other query parameters and submits as you type (debounced).
    Client tables (`client`): filters rows in place by their data-search text.
--}}
@if ($client)
    <x-search-input :name="$name" :placeholder="$placeholder" data-table-search {{ $attributes }} />
@else
    <form method="GET" action="{{ url()->current() }}" role="search" data-auto-submit {{ $attributes->only('class') }}>
        @foreach (request()->except([$name, 'page']) as $key => $preserved)
            @if (is_scalar($preserved))
                <input type="hidden" name="{{ $key }}" value="{{ $preserved }}" />
            @endif
        @endforeach
        <x-search-input :name="$name" :value="$value ?? request($name)" :placeholder="$placeholder" />
    </form>
@endif
