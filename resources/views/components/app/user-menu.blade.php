@php($user = auth()->user())

{{-- Shared account menu content used by the sidebar footer and the topbar. --}}
<div class="flex items-center gap-3 border-b border-line p-3">
    <x-avatar :name="$user->name" online />
    <div class="min-w-0">
        <p class="truncate text-sm font-semibold">{{ $user->name }}</p>
        <p class="truncate text-xs text-fg-3">{{ $user->email }}</p>
    </div>
</div>
<div class="p-1.5">
    <x-dropdown-item icon="user-round" :href="route('settings.edit')">{{ __('Your profile') }}</x-dropdown-item>
    <x-dropdown-item icon="key-round" :href="route('settings.edit').'#password'">{{ __('Change password') }}</x-dropdown-item>
</div>
<div class="space-y-3 border-t border-line p-3">
    <div>
        <p class="mb-2 text-xs font-semibold text-fg-3">{{ __('Theme') }}</p>
        <x-theme-switch />
    </div>
    <div class="hidden md:block">
        <p class="mb-2 text-xs font-semibold text-fg-3">{{ __('Layout') }}</p>
        <x-layout-switch />
    </div>
</div>
<form method="POST" action="{{ route('logout') }}" class="border-t border-line p-1.5">
    @csrf
    <x-dropdown-item type="submit" icon="log-out" danger>{{ __('Sign out') }}</x-dropdown-item>
</form>
