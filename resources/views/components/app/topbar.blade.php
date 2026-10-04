@props(['title' => null])

@php($user = auth()->user())

<header id="topbar" class="sticky top-0 z-30 border-b border-line bg-bg/85 backdrop-blur-md">
    <div class="mx-auto flex h-16 max-w-[1600px] items-center gap-2 px-4 sm:gap-3 sm:px-6 2xl:px-8">
        <x-icon-button icon="menu" :label="__('Open menu')" class="only-vertical -ml-2 md:hidden" data-sidebar-open />
        <x-icon-button icon="menu" :label="__('Open menu')" class="only-horizontal -ml-2 lg:hidden" data-sidebar-open />

        {{-- Horizontal layout: brand on the left. --}}
        <a href="{{ route('dashboard') }}" class="only-horizontal shrink-0 rounded-lg" aria-label="{{ config('app.name') }}, {{ __('go to dashboard') }}">
            <x-brand :subtitle="false" />
        </a>

        {{-- Horizontal layout: primary navigation inline with the brand (one-line header from lg up). --}}
        <nav aria-label="{{ __('Main navigation') }}" class="only-horizontal ml-2 hidden min-w-0 items-center gap-0.5 overflow-x-auto no-scrollbar lg:flex xl:ml-4">
            @foreach (collect(config('navigation'))->flatMap(fn ($group) => $group['items']) as $item)
                <x-app.nav-link :label="__($item['label'])" :icon="$item['icon']" :href="route($item['route'])"
                    :active="request()->routeIs($item['active'] ?? $item['route'])" :badge="$item['badge'] ?? null" />
            @endforeach
        </nav>

        {{-- Vertical layout: breadcrumb (the brand lives in the sidebar). --}}
        <nav aria-label="{{ __('Breadcrumb') }}" class="only-vertical hidden shrink-0 sm:block">
            <ol class="flex items-center gap-1.5 whitespace-nowrap text-sm">
                <li><a href="{{ route('dashboard') }}" class="rounded-md font-medium text-fg-2 transition-colors hover:text-fg">{{ config('app.name') }}</a></li>
                @if ($title)
                    <li class="text-fg-3" aria-hidden="true"><x-icon name="chevron-right" /></li>
                    <li><span aria-current="page" class="font-semibold text-fg">{{ $title }}</span></li>
                @endif
            </ol>
        </nav>
        <span class="only-vertical truncate text-[15px] font-bold sm:hidden">{{ $title }}</span>

        <div class="flex min-w-0 flex-1 justify-center px-2 horizontal:lg:justify-end">
            <button type="button" data-palette-open aria-keyshortcuts="Control+K Meta+K"
                class="hidden h-10 w-full max-w-[440px] items-center gap-2.5 rounded-lg border border-line bg-surface px-3 text-sm text-fg-3 shadow-card transition-colors hover:border-line-strong md:flex horizontal:lg:max-w-[260px]">
                <x-icon name="search" />
                <span class="flex-1 truncate text-left">{{ __('Search') }}<span class="horizontal:lg:hidden">{{ __(' pages, users and actions') }}</span></span>
                <x-kbd data-shortcut-label>Ctrl K</x-kbd>
            </button>
        </div>

        <div class="flex items-center gap-0.5 sm:gap-1">
            <x-icon-button icon="search" :label="__('Search')" class="md:hidden" data-palette-open />

            <button type="button" data-layout-toggle aria-label="{{ __('Switch layout') }}" data-tip="{{ __('Switch layout') }}"
                class="relative hidden size-10 shrink-0 place-items-center rounded-lg text-fg-2 transition-colors hover:bg-surface-2 hover:text-fg md:grid">
                <x-icon name="panel-left" class="only-horizontal size-[18px]" />
                <x-icon name="panel-top" class="only-vertical size-[18px]" />
            </button>

            <button type="button" data-theme-toggle aria-label="{{ __('Toggle theme') }}" data-tip="{{ __('Toggle theme') }}"
                class="relative hidden size-10 shrink-0 place-items-center rounded-lg text-fg-2 transition-colors hover:bg-surface-2 hover:text-fg sm:grid">
                <x-icon name="moon" class="size-[18px]" data-theme-icon="light" />
                <x-icon name="sun" class="hidden size-[18px]" data-theme-icon="dark" />
            </button>

            <x-dropdown width="w-[min(380px,calc(100vw-2rem))]" :label="__('Notifications')">
                <x-slot:trigger>
                    <x-icon-button icon="bell" :label="__('Notifications')" data-dropdown-trigger aria-haspopup="menu" aria-expanded="false" />
                </x-slot:trigger>

                <div class="flex items-center justify-between gap-2 px-4 pb-3 pt-3.5">
                    <h2 class="text-[15px] font-semibold">{{ __('Notifications') }}</h2>
                </div>
                <div class="border-t border-line">
                    <x-empty-state icon="bell-off" :title="__('You\'re all caught up')"
                        :text="__('Campaign updates, creator applications and payments will show up here.')" />
                </div>
            </x-dropdown>

            <span class="mx-1 hidden h-6 w-px bg-line sm:block" aria-hidden="true"></span>

            <x-dropdown :label="__('Account')">
                <x-slot:trigger>
                    <button type="button" data-dropdown-trigger aria-haspopup="menu" aria-expanded="false" aria-label="{{ __('Account menu') }}"
                        class="flex items-center gap-2 rounded-lg p-1 transition-colors hover:bg-surface-2 xl:pr-2">
                        <x-avatar :name="$user->name" size="sm" />
                        <span class="hidden whitespace-nowrap text-left xl:block">
                            <span class="block text-sm font-semibold leading-4">{{ $user->name }}</span>
                            <span class="block text-xs text-fg-3">{{ $user->email }}</span>
                        </span>
                        <x-icon name="chevron-down" class="hidden size-4 text-fg-3 xl:block" />
                    </button>
                </x-slot:trigger>

                <x-app.user-menu />
            </x-dropdown>
        </div>
    </div>

</header>
