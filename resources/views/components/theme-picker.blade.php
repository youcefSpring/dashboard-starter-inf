{{-- Large Light / Dark / System cards with previews (Settings → Appearance). Shares state with x-theme-switch. --}}
@php
    $preview = fn (bool $dark): string => $dark ? 'bg-[#0D0B17] ring-white/10' : 'bg-[#F8F7FC] ring-black/5';
@endphp

<div {{ $attributes->merge(['class' => 'grid grid-cols-3 gap-3']) }} role="group" aria-label="{{ __('Theme') }}">
    @foreach (['light' => __('Light'), 'dark' => __('Dark'), 'system' => __('System')] as $mode => $label)
        <button type="button" data-set-theme="{{ $mode }}" aria-pressed="false"
            class="rounded-lg border border-line p-2 text-left transition-colors hover:border-line-strong aria-pressed:border-primary aria-pressed:ring-2 aria-pressed:ring-primary/20">
            <span @class(['grid gap-0.5', 'grid-cols-2' => $mode === 'system', 'grid-cols-1' => $mode !== 'system'])>
                @foreach ($mode === 'system' ? [false, true] : [$mode === 'dark'] as $dark)
                    <span @class(['block h-16 overflow-hidden rounded-md ring-1 ring-inset', $preview($dark)])>
                        <span @class(['m-2 block h-2 w-10 max-w-[60%] rounded', 'bg-white/20' => $dark, 'bg-black/10' => ! $dark])></span>
                        <span class="mx-2 flex gap-1">
                            <span @class(['h-6 flex-1 rounded', 'bg-[#151223]' => $dark, 'bg-white' => ! $dark])></span>
                            <span class="h-6 flex-1 rounded bg-[#7638E0]"></span>
                        </span>
                    </span>
                @endforeach
            </span>
            <span class="mt-2 block text-center text-[13px] font-semibold">{{ $label }}</span>
        </button>
    @endforeach
</div>
