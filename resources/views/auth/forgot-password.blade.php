<x-layouts.auth :title="__('Forgot password')" :heading="__('Reset your password')"
    :description="__('Enter the email you signed up with and we\'ll send you a link to choose a new password.')">
    @if (session('status'))
        <x-alert class="mb-6" icon="mail-check">{{ session('status') }}</x-alert>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5" data-loading-form>
        @csrf

        <x-field name="email" type="email" :label="__('Email')" icon="mail" placeholder="you@company.com" required autofocus autocomplete="username" />

        <x-button type="submit" variant="primary" size="lg" class="w-full" data-loading-label="{{ __('Sending link') }}">{{ __('Email reset link') }}</x-button>
    </form>

    <p class="mt-8 text-center text-sm text-fg-2">
        <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 rounded-md font-semibold text-primary-ink hover:underline">
            <x-icon name="arrow-left" class="size-4" />{{ __('Back to sign in') }}
        </a>
    </p>
</x-layouts.auth>
