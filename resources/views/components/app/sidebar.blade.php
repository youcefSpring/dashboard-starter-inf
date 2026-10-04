@php($user = auth()->user())

<aside id="sidebar" class="fixed bottom-0 top-0 z-50 flex flex-col overflow-visible border-r border-line bg-surface" aria-label="{{ __('Primary') }}">
    <div class="sb-center flex h-16 shrink-0 items-center gap-3 border-b border-line px-4">
        <a href="{{ route('dashboard') }}" class="flex min-w-0 items-center rounded-lg" aria-label="{{ config('app.name') }}, {{ __('go to dashboard') }}">
            <x-brand />
        </a>
        <x-icon-button icon="x" :label="__('Close menu')" size="sm" class="sb-hide ml-auto md:hidden" data-sidebar-close />
    </div>

    <button type="button" id="sb-toggle" data-sidebar-toggle aria-label="{{ __('Collapse sidebar') }}"
        class="only-vertical absolute -right-3 top-[76px] z-10 hidden size-6 place-items-center rounded-full border border-line bg-surface text-fg-2 shadow-card transition-colors hover:text-fg md:grid">
        <x-icon name="chevron-left" class="size-3.5" stroke-width="2.25" data-expanded-icon />
        <x-icon name="chevron-right" class="hidden size-3.5" stroke-width="2.25" data-collapsed-icon />
    </button>

    <nav class="flex-1 overflow-y-auto overflow-x-hidden px-3 py-4 scroll-thin" aria-label="{{ __('Main navigation') }}">
        @foreach (config('navigation') as $group)
            <div @class(['mt-6' => ! $loop->first])>
                @if ($group['title'])
                    <p class="sb-hide px-3 pb-2 text-xs font-semibold text-fg-3">{{ __($group['title']) }}</p>
                    <div class="sb-divider mx-2 mb-3 hidden h-px bg-line"></div>
                @endif
                <div class="space-y-0.5">
                    @foreach ($group['items'] as $item)
                        <x-app.nav-item :label="__($item['label'])" :icon="$item['icon']" :href="route($item['route'])"
                            :active="request()->routeIs($item['active'] ?? $item['route'])" :badge="$item['badge'] ?? null" />
                    @endforeach
                </div>
            </div>
        @endforeach
    </nav>

    <div class="shrink-0 border-t border-line p-3">
        <x-dropdown align="start" position="top" :label="__('Account')">
            <x-slot:trigger>
                <button type="button" data-dropdown-trigger aria-haspopup="menu" aria-expanded="false"
                    data-tip="{{ $user->name }}" data-tip-side="right" data-tip-when="rail"
                    class="sb-center flex w-full items-center gap-3 rounded-lg p-2 text-left transition-colors hover:bg-surface-2">
                    <x-avatar :name="$user->name" size="sm" online />
                    <span class="sb-hide min-w-0 flex-1">
                        <span class="block truncate text-sm font-semibold">{{ $user->name }}</span>
                        <span class="block truncate text-xs text-fg-3">{{ $user->email }}</span>
                    </span>
                    <x-icon name="ellipsis-vertical" class="sb-hide size-4 text-fg-3" />
                </button>
            </x-slot:trigger>

            <x-app.user-menu />
        </x-dropdown>
    </div>
</aside>
