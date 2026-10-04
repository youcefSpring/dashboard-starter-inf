@props(['colspan' => 1, 'client' => false])

{{-- Empty-state row. With `client` it stays hidden until in-browser filtering leaves no rows. --}}
<tr @if ($client) data-table-empty hidden @endif class="hover:!bg-transparent">
    <td colspan="{{ $colspan }}">
        <div class="rounded-lg border border-dashed border-line">
            <x-empty-state {{ $attributes }}>{{ $slot }}</x-empty-state>
        </div>
    </td>
</tr>
