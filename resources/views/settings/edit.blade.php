<x-layouts.app :title="__('Settings')">
    <x-page-header :title="__('Settings')" :description="__('Manage your profile, password and preferences.')" />

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-[220px_minmax(0,1fr)]">
        <nav aria-label="{{ __('Settings sections') }}" class="lg:sticky lg:top-24 lg:self-start">
            <ul class="flex gap-1 overflow-x-auto no-scrollbar lg:flex-col">
                @foreach (['profile' => ['user-round', __('Profile')], 'password' => ['key-round', __('Password')], 'appearance' => ['palette', __('Appearance')], 'danger' => ['triangle-alert', __('Delete account')]] as $anchor => [$icon, $label])
                    <li>
                        <a href="#{{ $anchor }}" class="flex h-9 items-center gap-2.5 whitespace-nowrap rounded-lg px-3 text-sm font-semibold text-fg-2 transition-colors hover:bg-surface-2 hover:text-fg">
                            <x-icon :name="$icon" class="size-4" />{{ $label }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>

        <div class="max-w-3xl space-y-6">
            {{-- Profile --}}
            <x-card id="profile" :title="__('Profile')" :subtitle="__('Your name and the email address you sign in with.')" class="scroll-mt-24">
                <div class="mb-6 flex items-center gap-4">
                    <x-avatar :name="$user->name" size="xl" />
                    <div class="min-w-0">
                        <p class="truncate text-base font-semibold">{{ $user->name }}</p>
                        <p class="truncate text-[13px] text-fg-2">{{ __('Member since :date', ['date' => $user->created_at?->format('F Y')]) }}</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('settings.profile.update') }}" class="space-y-5" data-loading-form>
                    @csrf
                    @method('PATCH')

                    <div class="grid gap-5 sm:grid-cols-2">
                        <x-field name="name" :label="__('Full name')" :value="$user->name" required autocomplete="name" />
                        <x-field name="email" type="email" :label="__('Email')" :value="$user->email" required autocomplete="username" />
                    </div>

                    <div class="flex justify-end border-t border-line pt-4">
                        <x-button type="submit" variant="primary" data-loading-label="{{ __('Saving') }}">{{ __('Save profile') }}</x-button>
                    </div>
                </form>
            </x-card>

            {{-- Password --}}
            <x-card id="password" :title="__('Password')" :subtitle="__('Use a long, unique password you don\'t use anywhere else.')" class="scroll-mt-24">
                <form method="POST" action="{{ route('settings.password.update') }}" class="space-y-5" data-loading-form>
                    @csrf
                    @method('PUT')

                    <x-field name="current_password" type="password" bag="updatePassword" :label="__('Current password')" autocomplete="current-password" required />
                    <div class="grid gap-5 sm:grid-cols-2">
                        <x-field name="password" id="new_password" type="password" bag="updatePassword" :label="__('New password')" autocomplete="new-password" required
                            :hint="__('At least 8 characters.')" />
                        <x-field name="password_confirmation" id="new_password_confirmation" type="password" bag="updatePassword" :label="__('Confirm new password')" autocomplete="new-password" required />
                    </div>

                    <div class="flex justify-end border-t border-line pt-4">
                        <x-button type="submit" variant="primary" data-loading-label="{{ __('Updating') }}">{{ __('Update password') }}</x-button>
                    </div>
                </form>
            </x-card>

            {{-- Appearance --}}
            <x-card id="appearance" :title="__('Appearance')" :subtitle="__('Saved on this device.')" class="scroll-mt-24">
                <h3 class="text-sm font-semibold">{{ __('Theme') }}</h3>
                <p class="mt-0.5 text-xs text-fg-2">{{ __('System follows your operating system setting.') }}</p>
                <x-theme-picker class="mt-3 max-w-lg" />

                <div class="mt-6 hidden border-t border-line pt-5 md:block">
                    <h3 class="text-sm font-semibold">{{ __('Navigation layout') }}</h3>
                    <p class="mt-0.5 text-xs text-fg-2">{{ __('Top navigation bar, or a collapsible sidebar. Phones always use a slide-out menu.') }}</p>
                    <x-layout-switch class="mt-3 max-w-xs" />
                </div>
            </x-card>

            {{-- Danger zone --}}
            <x-card id="danger" :title="__('Delete account')" :subtitle="__('Permanently remove your account and all of its data.')" class="scroll-mt-24 border-error/30">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <p class="max-w-md text-[13px] text-fg-2">{{ __('This can\'t be undone. You\'ll be signed out immediately and your data will be removed.') }}</p>
                    <x-button variant="danger" icon="trash-2" data-dialog-open="delete-account-dialog">{{ __('Delete account') }}</x-button>
                </div>
            </x-card>
        </div>
    </div>

    <x-modal id="delete-account-dialog" :title="__('Delete your account?')" :description="__('Enter your password to confirm. This permanently deletes your account.')"
        icon="trash-2" tone="error" size="sm:max-w-md" :data-open-on-load="$errors->userDeletion->isNotEmpty() ? 'true' : null">
        <form id="delete-account-form" method="POST" action="{{ route('settings.profile.destroy') }}" data-loading-form>
            @csrf
            @method('DELETE')
            <x-field name="password" id="delete_password" type="password" bag="userDeletion" :label="__('Password')" autocomplete="current-password" required />
        </form>

        <x-slot:footer>
            <x-button data-dialog-close>{{ __('Cancel') }}</x-button>
            <x-button type="submit" form="delete-account-form" variant="danger" data-loading-label="{{ __('Deleting') }}">{{ __('Delete account') }}</x-button>
        </x-slot:footer>
    </x-modal>
</x-layouts.app>
