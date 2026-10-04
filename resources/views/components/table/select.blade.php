@props(['value', 'label' => null, 'disabled' => false])

{{-- Row checkbox; `value` (usually the id) is what bulk forms receive as ids[]. --}}
<td class="!pr-1">
    <input type="checkbox" data-table-select value="{{ $value }}" aria-label="{{ $label ?? __('Select row') }}" @disabled($disabled)
        class="size-4 cursor-pointer rounded border-line accent-primary disabled:cursor-not-allowed disabled:opacity-40" />
</td>
