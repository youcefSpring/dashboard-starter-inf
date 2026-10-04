@props(['options' => [10, 25, 50], 'value' => 10, 'name' => 'per_page'])

{{-- "Rows per page" select for server tables; keeps the other query parameters. --}}
<form method="GET" action="{{ url()->current() }}" class="flex items-center gap-2 text-[13px] text-fg-2" data-auto-submit>
    @foreach (request()->except([$name, 'page']) as $key => $preserved)
        @if (is_scalar($preserved))
            <input type="hidden" name="{{ $key }}" value="{{ $preserved }}" />
        @endif
    @endforeach
    <label for="table-{{ $name }}" class="whitespace-nowrap">{{ __('Rows per page') }}</label>
    <select id="table-{{ $name }}" name="{{ $name }}" class="form-control num h-8 w-auto py-0 pl-2.5 pr-8 shadow-none">
        @foreach ($options as $option)
            <option value="{{ $option }}" @selected((int) $value === $option)>{{ $option }}</option>
        @endforeach
    </select>
</form>
