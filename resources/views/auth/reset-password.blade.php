<x-layouts.auth :title="__('Choose a new password')" :heading="__('Choose a new password')"
    :description="__('Pick something you haven\'t used before. You\'ll be signed out everywhere else.')">
    <form method="POST" action="{{ route('password.store') }}" class="space-y-5" data-loading-form>
        @csrf
        <input type="hidden" name="token" value="{{ $token }}" />

        <x-field name="email" type="email" :label="__('Email')" icon="mail" :value="$email" required autocomplete="username" />

        <x-field name="password" type="password" :label="__('New password')" icon="lock" required autofocus autocomplete="new-password"
            :hint="__('At least 8 characters.')" />

        <x-field name="password_confirmation" type="password" :label="__('Confirm new password')" icon="lock" required autocomplete="new-password" />

        <x-button type="submit" variant="primary" size="lg" class="w-full" data-loading-label="{{ __('Saving') }}">{{ __('Reset password') }}</x-button>
    </form>

    <p class="mt-8 text-center text-sm text-fg-2">
        <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 rounded-md font-semibold text-primary-ink hover:underline">
            <x-icon name="arrow-left" class="size-4" />{{ __('Back to sign in') }}
        </a>
    </p>
</x-layouts.auth>
