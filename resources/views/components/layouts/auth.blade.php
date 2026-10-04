@props(['title', 'heading', 'description' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    @include('partials.head', ['title' => $title])
</head>
<body class="min-h-full font-sans text-[14px] leading-normal antialiased">
    <div class="grid min-h-dvh lg:grid-cols-[minmax(0,1fr)_minmax(0,1.1fr)]">
        {{-- Brand panel --}}
        <aside class="brand-grad relative hidden overflow-hidden text-white lg:flex lg:flex-col lg:justify-between lg:p-10 xl:p-14">
            <div class="pointer-events-none absolute -right-32 -top-32 size-[460px] rounded-full bg-white/10 blur-3xl" aria-hidden="true"></div>
            <div class="pointer-events-none absolute -bottom-40 -left-24 size-[420px] rounded-full bg-white/10 blur-3xl" aria-hidden="true"></div>

            <a href="{{ route('login') }}" class="relative w-fit rounded-lg">
                <x-brand size="lg" inverse />
            </a>

            <div class="relative max-w-md">
                <h2 class="text-[34px] font-extrabold leading-[1.15] tracking-tight xl:text-[40px]">{{ __('Every creator campaign, in one calm place.') }}</h2>
                <p class="mt-4 text-[15px] leading-relaxed text-white/80">
                    {{ __('Plan campaigns, track reach and engagement, and keep budgets on pace without juggling spreadsheets.') }}
                </p>

                <div class="mt-8 rounded-2xl bg-white/12 p-5 ring-1 ring-inset ring-white/20 backdrop-blur-md">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-[13px] font-semibold text-white/80">{{ __('Total reach, last 30 days') }}</p>
                        <span class="num inline-flex items-center gap-0.5 rounded-md bg-white/20 px-1.5 py-0.5 text-xs font-semibold">
                            <x-icon name="arrow-up-right" class="size-3.5" stroke-width="2.25" />+18.4%
                        </span>
                    </div>
                    <p class="num mt-1 text-[32px] font-bold leading-10 tracking-tight">12.8M</p>
                    <div class="mt-4 flex h-16 items-end gap-1.5" aria-hidden="true">
                        @foreach ([38, 52, 44, 61, 57, 72, 66, 80, 74, 92, 86, 100] as $height)
                            <span class="flex-1 rounded-t-[4px] {{ $loop->last ? 'bg-white' : 'bg-white/35' }}" style="height: {{ $height }}%"></span>
                        @endforeach
                    </div>
                </div>
            </div>

            <ul class="relative grid max-w-md gap-3 text-sm text-white/85">
                @foreach ([['shield-check', __('Secure sign-in with rate limiting and session protection')], ['moon', __('Light and dark themes that follow your system')], ['smartphone', __('Responsive from phone to ultrawide')]] as [$icon, $text])
                    <li class="flex items-center gap-3">
                        <span class="grid size-7 place-items-center rounded-lg bg-white/15"><x-icon :name="$icon" class="size-4" /></span>{{ $text }}
                    </li>
                @endforeach
            </ul>
        </aside>

        {{-- Form panel --}}
        <main class="flex min-h-dvh flex-col px-4 py-6 sm:px-8">
            <div class="flex items-center justify-between gap-3">
                <a href="{{ route('login') }}" class="rounded-lg lg:invisible" aria-label="{{ config('app.name') }}">
                    <x-brand :subtitle="false" />
                </a>
                <button type="button" data-theme-toggle aria-label="{{ __('Toggle theme') }}"
                    class="grid size-10 place-items-center rounded-lg text-fg-2 transition-colors hover:bg-surface-2 hover:text-fg">
                    <x-icon name="moon" class="size-[18px]" data-theme-icon="light" />
                    <x-icon name="sun" class="hidden size-[18px]" data-theme-icon="dark" />
                </button>
            </div>

            <div class="flex flex-1 items-center justify-center py-10">
                <div class="w-full max-w-[400px]">
                    <h1 class="text-2xl font-bold tracking-tight sm:text-[28px] sm:leading-9">{{ $heading }}</h1>
                    @if ($description)
                        <p class="mt-2 text-[15px] text-fg-2">{{ $description }}</p>
                    @endif

                    <div class="mt-8">
                        {{ $slot }}
                    </div>
                </div>
            </div>

            <p class="text-center text-xs text-fg-3">&copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('All rights reserved.') }}</p>
        </main>
    </div>
</body>
</html>
