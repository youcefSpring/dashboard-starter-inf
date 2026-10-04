<x-layouts.auth :title="__('Sign in')" :heading="__('Welcome back')" :description="__('Sign in to pick up where you left off.')">
    @if (session('status'))
        <x-alert class="mb-6">{{ session('status') }}</x-alert>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5" data-loading-form>
        @csrf

        <x-field name="email" type="email" :label="__('Email')" icon="mail" placeholder="you@company.com" required autofocus autocomplete="username" />

        <x-field name="password" type="password" :label="__('Password')" icon="lock" required autocomplete="current-password">
            <x-slot:aside>
                <a href="{{ route('password.request') }}" class="ml-auto rounded-md text-[13px] font-semibold text-primary-ink hover:underline">{{ __('Forgot password?') }}</a>
            </x-slot:aside>
        </x-field>

        <x-checkbox name="remember" :label="__('Keep me signed in')" />

        <x-button type="submit" variant="primary" size="lg" class="w-full" data-loading-label="{{ __('Signing in') }}">{{ __('Sign in') }}</x-button>
    </form>

    <p class="mt-8 text-center text-sm text-fg-2">
        {{ __('New here?') }}
        <a href="{{ route('register') }}" class="rounded-md font-semibold text-primary-ink hover:underline">{{ __('Create an account') }}</a>
    </p>
</x-layouts.auth>
