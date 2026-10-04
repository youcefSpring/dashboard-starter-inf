@props(['post'])

{{--
    Published content card. `post` keys: title, type (Reel, TikTok, Post, Story, YouTube), platform, icon, tile,
    duration (nullable), views, likes, comments, shares, saves, engagement_rate, creator (['name', 'tile']).
--}}
@php
    use Illuminate\Support\Number;
    $p = $post;
    $isVideo = $p['type'] !== 'Post';
@endphp

<article {{ $attributes->merge(['class' => 'flex flex-col overflow-hidden rounded-xl border border-line bg-surface shadow-card']) }}>
    <div class="relative aspect-[16/10] {{ config('ui.tiles')[$p['tile']] }}">
        <span class="absolute inset-0 grid place-items-center">
            <span class="grid size-14 place-items-center rounded-full bg-white/25 ring-1 ring-white/40"><x-icon :name="$p['icon']" class="size-7" stroke-width="1.5" /></span>
        </span>
        <span class="absolute left-2.5 top-2.5 inline-flex items-center gap-1 rounded-md bg-black/55 px-1.5 py-0.5 text-[11px] font-semibold text-white backdrop-blur-sm">{{ $p['type'] }}</span>
        <span class="num absolute bottom-2.5 left-2.5 inline-flex items-center gap-1 rounded-md bg-black/55 px-1.5 py-0.5 text-[11px] font-semibold text-white backdrop-blur-sm">
            <x-icon :name="$isVideo ? 'play' : 'eye'" class="size-3" />{{ Number::abbreviate($p['views'], maxPrecision: 1) }}<span class="sr-only"> {{ __('views') }}</span>
        </span>
        @if ($p['duration'])
            <span class="num absolute bottom-2.5 right-2.5 rounded-md bg-black/55 px-1.5 py-0.5 text-[11px] font-semibold text-white backdrop-blur-sm">{{ $p['duration'] }}</span>
        @endif
    </div>
    <div class="flex flex-1 flex-col p-3.5">
        <h3 class="line-clamp-2 text-sm font-semibold leading-5">{{ $p['title'] }}</h3>
        <p class="mt-2 flex min-w-0 items-center gap-2 text-xs text-fg-2">
            <x-avatar :name="$p['creator']['name']" :tile="$p['creator']['tile']" size="xs" /><span class="truncate font-medium">{{ $p['creator']['name'] }}</span>
        </p>
        <dl class="mt-3 grid grid-cols-4 gap-1 border-t border-line pt-3 text-center">
            @foreach ([['heart', __('Likes'), $p['likes']], ['message-circle', __('Comments'), $p['comments']], ['share-2', __('Shares'), $p['shares']], ['bookmark', __('Saves'), $p['saves']]] as [$icon, $label, $value])
                <div class="min-w-0">
                    <dt class="flex items-center justify-center text-fg-3"><x-icon :name="$icon" class="size-3" /><span class="sr-only">{{ $label }}</span></dt>
                    <dd class="num mt-0.5 text-xs font-semibold" title="{{ $label }}">{{ $value ? Number::abbreviate($value, maxPrecision: 1) : '—' }}</dd>
                </div>
            @endforeach
        </dl>
        <div class="mt-3 flex items-center justify-between rounded-lg bg-surface-2 px-2.5 py-1.5 text-xs">
            <span class="text-fg-2">{{ __('Engagement rate') }}</span>
            <b @class(['num font-bold', 'text-success' => $p['engagement_rate'] >= 9, 'text-fg' => $p['engagement_rate'] < 9])>{{ $p['engagement_rate'] }}%</b>
        </div>
    </div>
</article>
