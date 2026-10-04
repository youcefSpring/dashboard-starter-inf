{{--
    Command palette (Ctrl/⌘ K or any [data-palette-open]). Items are server-rendered and filtered
    client-side by resources/js/palette.js. Add actions below; pages come from config/navigation.php.
--}}
<dialog id="command-palette" aria-label="{{ __('Search') }}"
    class="modal m-0 mx-auto mt-[10vh] w-[calc(100%-1.5rem)] max-w-xl overflow-hidden rounded-2xl border border-line bg-surface p-0 text-fg shadow-pop sm:mt-[12vh]">
    <div class="flex max-h-[70vh] flex-col">
        <div class="flex items-center gap-3 border-b border-line px-4">
            <x-icon name="search" class="size-5 text-fg-3" />
            <input type="text" data-palette-input role="combobox" aria-expanded="true" aria-controls="palette-list" aria-autocomplete="list" autocomplete="off"
                placeholder="{{ __('Search pages, users and actions') }}"
                class="h-14 min-w-0 flex-1 bg-transparent text-[15px] text-fg placeholder:text-fg-3 focus:outline-hidden" />
            <x-kbd class="hidden sm:block">Esc</x-kbd>
        </div>

        <div id="palette-list" role="listbox" aria-label="{{ __('Results') }}" class="flex-1 overflow-y-auto px-2 pb-2 scroll-thin">
            <div data-palette-group class="pt-2">
                <p class="px-2 pb-1 text-xs font-semibold text-fg-3">{{ __('Pages') }}</p>
                @foreach (collect(config('navigation'))->flatMap(fn ($group) => $group['items']) as $item)
                    <a href="{{ route($item['route']) }}" role="option" data-palette-item data-keywords="{{ __($item['label']) }}"
                        class="flex items-center gap-3 rounded-lg px-2 py-2 text-sm font-medium text-fg aria-selected:bg-primary-soft aria-selected:text-primary-ink">
                        <x-icon :name="$item['icon']" class="size-4 text-fg-3" /><span class="flex-1 truncate">{{ __($item['label']) }}</span>
                    </a>
                @endforeach
            </div>

            <div data-palette-group class="pt-2">
                <p class="px-2 pb-1 text-xs font-semibold text-fg-3">{{ __('Actions') }}</p>
                <a href="{{ route('users.index') }}" role="option" data-palette-item data-palette-search="{{ route('users.index') }}" data-keywords="{{ __('Search users find people') }}"
                    class="flex items-center gap-3 rounded-lg px-2 py-2 text-sm font-medium text-fg aria-selected:bg-primary-soft aria-selected:text-primary-ink">
                    <x-icon name="user-search" class="size-4 text-fg-3" /><span class="flex-1 truncate" data-palette-search-label>{{ __('Search users') }}</span>
                </a>
                <button type="button" role="option" data-palette-item data-theme-toggle data-keywords="{{ __('Toggle theme dark light mode') }}"
                    class="flex w-full items-center gap-3 rounded-lg px-2 py-2 text-left text-sm font-medium text-fg aria-selected:bg-primary-soft aria-selected:text-primary-ink">
                    <x-icon name="sun-moon" class="size-4 text-fg-3" /><span class="flex-1 truncate">{{ __('Toggle light / dark theme') }}</span>
                </button>
                <button type="button" role="option" data-palette-item data-layout-toggle data-keywords="{{ __('Switch layout sidebar top navigation horizontal vertical') }}"
                    class="hidden w-full items-center gap-3 rounded-lg px-2 py-2 text-left text-sm font-medium text-fg aria-selected:bg-primary-soft aria-selected:text-primary-ink md:flex">
                    <x-icon name="panels-top-left" class="size-4 text-fg-3" /><span class="flex-1 truncate">{{ __('Switch sidebar / top navigation') }}</span>
                </button>
                <a href="{{ route('settings.edit') }}#password" role="option" data-palette-item data-keywords="{{ __('Change password security') }}"
                    class="flex items-center gap-3 rounded-lg px-2 py-2 text-sm font-medium text-fg aria-selected:bg-primary-soft aria-selected:text-primary-ink">
                    <x-icon name="key-round" class="size-4 text-fg-3" /><span class="flex-1 truncate">{{ __('Change password') }}</span>
                </a>
            </div>

            <div data-palette-empty hidden class="px-6 py-10 text-center">
                <p class="text-sm font-semibold">{{ __('No results') }}</p>
                <p class="mt-1 text-[13px] text-fg-2">{{ __('Try a page name or an action.') }}</p>
            </div>
        </div>

        <div class="hidden items-center gap-4 border-t border-line px-4 py-2.5 text-xs text-fg-3 sm:flex">
            <span>{{ __('↑ ↓ to move') }}</span><span>{{ __('Enter to open') }}</span><span>{{ __('Esc to close') }}</span>
        </div>
    </div>
</dialog>
