@props(['id', 'active' => false])

<div id="{{ $id }}" role="tabpanel" tabindex="0" @unless ($active) hidden @endunless {{ $attributes }}>
    {{ $slot }}
</div>
