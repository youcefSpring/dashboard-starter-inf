<x-layouts.auth :title="__('Create account')" :heading="__('Create your account')" :description="__('Set up your workspace in under a minute.')">
    <form method="POST" action="{{ route('register') }}" class="space-y-5" data-loading-form>
        @csrf

        <x-field name="name" :label="__('Full name')" icon="user-round" placeholder="Sarah Benali" required autofocus autocomplete="name" />

        <x-field name="email" type="email" :label="__('Work email')" icon="mail" placeholder="you@company.com" required autocomplete="username" />

        <x-field name="password" type="password" :label="__('Password')" icon="lock" required autocomplete="new-password"
            :hint="__('At least 8 characters.')" />

        <x-field name="password_confirmation" type="password" :label="__('Confirm password')" icon="lock" required autocomplete="new-password" />

        <x-button type="submit" variant="primary" size="lg" class="w-full" data-loading-label="{{ __('Creating account') }}">{{ __('Create account') }}</x-button>
    </form>

    <p class="mt-8 text-center text-sm text-fg-2">
        {{ __('Already have an account?') }}
        <a href="{{ route('login') }}" class="rounded-md font-semibold text-primary-ink hover:underline">{{ __('Sign in') }}</a>
    </p>
</x-layouts.auth>
