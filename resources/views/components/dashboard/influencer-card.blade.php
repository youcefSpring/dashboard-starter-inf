@props(['influencer'])

{{--
    Creator discovery card. `influencer` keys: name, handle, platform, tile, category, location, verified,
    followers, engagement_rate, avg_views, reach, female (percent), age, languages (list), rate.
--}}
@php
    use Illuminate\Support\Number;
    $i = $influencer;
@endphp

<article {{ $attributes->merge(['class' => 'flex flex-col overflow-hidden rounded-xl border border-line bg-surface shadow-card transition-colors hover:border-line-strong']) }}>
    <div class="relative h-[72px] {{ config('ui.tiles')[$i['tile']] }}">
        <span class="absolute left-3 top-3 rounded-md bg-surface/90 px-1.5 py-0.5 text-[11px] font-semibold text-fg">{{ $i['category'] }}</span>
        {{ $corner ?? '' }}
    </div>
    <div class="-mt-8 px-4"><x-avatar :name="$i['name']" :tile="$i['tile']" size="lg" ring /></div>
    <div class="flex flex-1 flex-col px-4 pb-4 pt-2.5">
        <h3 class="flex items-center gap-1 text-[15px] font-semibold"><span class="truncate">{{ $i['name'] }}</span>@if ($i['verified'] ?? false)<x-verified />@endif</h3>
        <p class="mt-0.5 flex items-center gap-2 text-xs text-fg-3"><span class="truncate">{{ '@'.$i['handle'] }}</span><x-platform-chip :platform="$i['platform']" /></p>
        <p class="mt-1.5 flex items-center gap-1 text-xs text-fg-2"><x-icon name="map-pin" class="size-3.5 text-fg-3" />{{ $i['location'] }}</p>

        <dl class="mt-3 grid grid-cols-2 gap-2">
            <x-stat variant="tile" :label="__('Followers')" :value="Number::abbreviate($i['followers'], maxPrecision: 1)" class="px-2.5 py-2" />
            <x-stat variant="tile" :label="__('Engagement')" :value="$i['engagement_rate'].'%'" class="px-2.5 py-2" />
            <x-stat variant="tile" :label="__('Avg. views')" :value="Number::abbreviate($i['avg_views'], maxPrecision: 1)" class="px-2.5 py-2" />
            <x-stat variant="tile" :label="__('Est. reach')" :value="Number::abbreviate($i['reach'], maxPrecision: 1)" class="px-2.5 py-2" />
        </dl>

        <div class="mt-3">
            <div class="flex items-center justify-between text-xs">
                <span class="font-medium text-fg-2">{{ __('Audience') }}</span>
                <span class="text-fg-2"><b class="num font-semibold text-fg">{{ $i['female'] }}%</b> {{ __('women, mostly :age', ['age' => $i['age']]) }}</span>
            </div>
            <x-stack-bar class="mt-1.5" height="h-1.5" :segments="[
                ['label' => __('Women'), 'value' => $i['female'], 'color' => 'c1'],
                ['label' => __('Men'), 'value' => 100 - $i['female'], 'color' => 'c2'],
            ]" />
            <p class="mt-2 flex items-center gap-1 text-xs text-fg-2"><x-icon name="languages" class="size-3.5 text-fg-3" />{{ implode(', ', $i['languages']) }}</p>
        </div>

        <p class="mt-3 text-xs text-fg-2">{{ __('From') }} <b class="num text-sm font-bold text-fg">{{ Number::currency($i['rate'], precision: 0) }}</b> {{ __('per post') }}</p>

        @isset($actions)
            <div class="mt-auto grid grid-cols-2 gap-2 pt-4">{{ $actions }}</div>
        @endisset
    </div>
</article>
