@props(['campaign'])

{{-- Compact campaign summary (same keys as DashboardController::recentCampaigns()). --}}
@php
    use Illuminate\Support\Number;
    $c = $campaign;
    $spentPct = $c['budget'] ? $c['spent'] / $c['budget'] * 100 : 0;
@endphp

<article {{ $attributes->merge(['class' => 'rounded-xl border border-line bg-surface p-4 transition-colors hover:border-line-strong']) }}>
    <div class="flex items-start gap-3">
        <x-avatar :name="$c['client']" :tile="$c['client_tile']" size="sm" square />
        <div class="min-w-0 flex-1">
            <p class="truncate text-sm font-semibold">{{ $c['name'] }}</p>
            <p class="truncate text-xs text-fg-3">{{ $c['client'] }}</p>
        </div>
        <x-status-badge :status="$c['status']" />
    </div>
    <div class="mt-3 flex items-center justify-between text-xs">
        <span class="text-fg-2"><b class="num font-semibold text-fg">{{ Number::currency($c['spent'], precision: 0) }}</b> {{ __('of') }} {{ Number::currency($c['budget'], precision: 0) }}</span>
        <span class="num text-fg-3">{{ round($spentPct) }}%</span>
    </div>
    <x-progress :value="$spentPct" height="h-1" class="mt-1.5" />
    <div class="mt-3 flex items-center justify-between gap-2">
        <x-avatar-stack :people="$c['influencers']" />
        <dl class="flex gap-4 text-right text-xs">
            <div><dt class="text-fg-3">{{ __('Reach') }}</dt><dd class="num font-semibold">{{ $c['reach'] ? Number::abbreviate($c['reach'], maxPrecision: 1) : '—' }}</dd></div>
            <div><dt class="text-fg-3">{{ __('ER') }}</dt><dd class="num font-semibold">{{ $c['engagement_rate'] ? $c['engagement_rate'].'%' : '—' }}</dd></div>
            <div><dt class="text-fg-3">{{ __('ROI') }}</dt><dd class="num font-semibold">{{ $c['roi'] !== null ? $c['roi'].'%' : '—' }}</dd></div>
        </dl>
    </div>
</article>
