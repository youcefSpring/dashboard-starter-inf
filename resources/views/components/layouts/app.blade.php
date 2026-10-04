@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    @include('partials.head', ['title' => $title])
</head>
<body class="min-h-full font-sans text-[14px] leading-normal antialiased">
    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[100] focus:rounded-lg focus:bg-surface focus:px-4 focus:py-2 focus:shadow-pop">
        {{ __('Skip to content') }}
    </a>

    <div id="sb-overlay" class="scrim fade-in fixed inset-0 z-40 hidden" data-sidebar-close></div>
    <x-app.sidebar />

    <div id="shell" class="min-h-full">
        <x-app.topbar :title="$title" />

        <main id="main" tabindex="-1" class="mx-auto w-full max-w-[1600px] px-4 pb-12 pt-6 sm:px-6 2xl:px-8">
            {{ $slot }}
        </main>
    </div>

    <x-command-palette />
    <x-toasts />
    <div id="tooltip" role="tooltip" class="pointer-events-none fixed z-[99] hidden max-w-[240px] rounded-md bg-fg px-2 py-1 text-xs font-medium text-bg shadow-pop"></div>
</body>
</html>
