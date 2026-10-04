@php
    use Illuminate\Support\Number;

    $hour = now()->hour;
    $greeting = $hour < 12 ? __('Good morning') : ($hour < 18 ? __('Good afternoon') : __('Good evening'));

    $money = fn (float|int $amount): string => Number::currency($amount, precision: 0);

    $budgetSpentPct = $budget['spent'] / $budget['total'] * 100;
    $budgetPacePct = $budget['year_elapsed'] * 100;
    $underPace = $budget['total'] * $budget['year_elapsed'] - $budget['spent'];
    $lastMonth = $budget['monthly'][array_key_last($budget['monthly'])];
    $prevMonth = $budget['monthly'][array_key_last($budget['monthly']) - 1];

    $statusTotal = array_sum(array_column($statusOverview, 'count'));
    $statusColors = ['Active' => 'success', 'Pending approval' => 'info', 'Draft' => 'neutral', 'Paused' => 'warning', 'Completed' => 'primary'];

    $totals = $performance['totals'];
    $previous = $performance['previous'];
    $objectiveIcons = ['Awareness' => 'eye', 'Engagement' => 'heart', 'Conversions' => 'shopping-bag'];

    $roiMultiple = $roi['revenue'] / $roi['spend'];
    $costPerAcquisition = $roi['spend'] / $roi['conversions'];
    $costPerEngagement = $roi['spend'] / $roi['engagements'];
    $conversionRate = $roi['conversions'] / $roi['engagements'] * 100;

    $unreadThreads = collect($threads)->where('unread', '>', 0)->count();
@endphp

<x-layouts.app :title="__('Dashboard')">
    <x-page-header :title="$greeting.', '.auth()->user()->firstName().' 👋'" :description="__('Here\'s what\'s happening with your influencer campaigns.')">
        <x-slot:meta>
            <div class="mt-3 flex flex-wrap gap-2">
                <x-badge tone="info" icon="file-check-2">{{ __('3 posts waiting for your approval') }}</x-badge>
            </div>
        </x-slot:meta>
        <x-slot:actions>
            <x-date-range-picker :presets="collect($periods)->mapWithKeys(fn ($days) => [$days => __('Last :count days', ['count' => $days])])->all()"
                :selected="$period" :from="$from" :to="$to" :action="route('dashboard')" />
            <x-button :href="route('users.index')" variant="primary" icon="users">{{ __('Manage users') }}</x-button>
        </x-slot:actions>
    </x-page-header>

    {{-- KPIs --}}
    <section aria-label="{{ __('Key metrics') }}" class="mt-6 grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-4">
        @foreach ($kpis as $kpi)
            <x-dashboard.kpi-card :kpi="$kpi" />
        @endforeach
    </section>

    <div class="mt-4 grid grid-cols-1 gap-4 xl:grid-cols-12">
        {{-- Performance chart --}}
        <x-card :title="__('Campaign performance')" :subtitle="__('Daily reach, engagement and conversions across all live campaigns')"
            class="xl:col-span-8" body-class="flex flex-col px-4 pb-4 pt-4 sm:px-5 sm:pb-5">
            <x-slot:actions>
                <x-tabs :label="__('Chart period')">
                    @foreach ($periods as $days)
                        <x-tab :href="route('dashboard', ['period' => $days])" :active="$days === $period">{{ __(':count days', ['count' => $days]) }}</x-tab>
                    @endforeach
                </x-tabs>
            </x-slot:actions>

            <x-line-chart class="flex-1" :labels="$performance['labels']" :label="__('Reach, engagement and conversions over :count days', ['count' => count($performance['labels'])])" :series="[
                ['key' => 'reach', 'label' => __('Reach'), 'color' => 'c1', 'values' => $performance['series']['reach']],
                ['key' => 'engagement', 'label' => __('Engagement'), 'color' => 'c2', 'values' => $performance['series']['engagement']],
                ['key' => 'conversions', 'label' => __('Conversions'), 'color' => 'c3', 'values' => $performance['series']['conversions']],
            ]" />

            <dl class="mt-4 grid grid-cols-2 gap-4 border-t border-line pt-4 lg:grid-cols-4 lg:gap-0 lg:divide-x lg:divide-line">
                <x-stat :label="__('Reach')" :value="Number::abbreviate($totals['reach'], maxPrecision: 1)" class="lg:px-4 lg:first:pl-0">
                    <x-change :current="$totals['reach']" :previous="$previous['reach']" />
                </x-stat>
                <x-stat :label="__('Engagements')" :value="Number::abbreviate($totals['engagement'], maxPrecision: 1)" class="lg:px-4">
                    <x-change :current="$totals['engagement']" :previous="$previous['engagement']" />
                </x-stat>
                <x-stat :label="__('Conversions')" :value="number_format($totals['conversions'])" class="lg:px-4">
                    <x-change :current="$totals['conversions']" :previous="$previous['conversions']" />
                </x-stat>
                <x-stat :label="__('Engagement rate')" :value="number_format($totals['engagement'] / $totals['reach'] * 100, 2).'%'" class="lg:px-4">
                    <x-change :current="$totals['engagement'] / $totals['reach'] * 100" :previous="$previous['engagement'] / $previous['reach'] * 100" points />
                </x-stat>
            </dl>
        </x-card>

        {{-- Budget --}}
        <x-card :title="__('Budget overview')" :subtitle="__('FY :year, all clients', ['year' => now()->year])" class="xl:col-span-4">
            <div class="flex items-end justify-between gap-3">
                <div>
                    <p class="text-[13px] font-medium text-fg-2">{{ __('Total budget') }}</p>
                    <p class="num mt-0.5 text-[28px] font-bold leading-9 tracking-tight">{{ $money($budget['total']) }}</p>
                </div>
                <x-badge :tone="$underPace >= 0 ? 'success' : 'warning'" dot>{{ $underPace >= 0 ? __('Under pace') : __('Over pace') }}</x-badge>
            </div>

            <div class="mt-4">
                <x-progress :value="$budgetSpentPct" height="h-2.5" :marker="$budgetPacePct" :label="__('Budget spent')" />
                <div class="mt-2 flex justify-between gap-2 text-xs text-fg-2">
                    <span><b class="num font-semibold text-fg">{{ number_format($budgetSpentPct, 1) }}%</b> {{ __('spent') }}</span>
                    <span class="text-right">{{ __('Marker: :pct% of the year elapsed', ['pct' => round($budgetPacePct)]) }}</span>
                </div>
            </div>

            <dl class="mt-4 grid grid-cols-2 gap-2">
                <x-stat variant="tile" dot="bg-primary" :label="__('Spent')" :value="$money($budget['spent'])" />
                <x-stat variant="tile" dot="bg-surface-3 ring-1 ring-line-strong" :label="__('Remaining')" :value="$money($budget['total'] - $budget['spent'])" />
            </dl>

            <div class="mt-5">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <p class="text-[13px] font-semibold">{{ __('Monthly spend') }}</p>
                    <p class="flex items-center gap-1.5 text-xs text-fg-2">
                        {{ $lastMonth['month'] }} <b class="num font-semibold text-fg">{{ $money($lastMonth['amount']) }}</b>
                        <x-change :current="$lastMonth['amount']" :previous="$prevMonth['amount']" neutral />
                    </p>
                </div>
                <x-bar-chart class="mt-3" :items="collect($budget['monthly'])->map(fn ($m) => ['label' => $m['month'], 'value' => $m['amount'], 'display' => $money($m['amount'])])->all()" />
            </div>

            <p class="mt-4 text-xs text-fg-2">
                @if ($underPace >= 0)
                    {!! __('You\'re :amount under the expected pace for this point in the year.', ['amount' => '<b class="num font-semibold text-fg">'.e($money($underPace)).'</b>']) !!}
                @else
                    {!! __('Spending is :amount ahead of pace.', ['amount' => '<b class="num font-semibold text-fg">'.e($money(-$underPace)).'</b>']) !!}
                @endif
            </p>
        </x-card>
    </div>

    {{-- Recent campaigns: in-browser data table (search, filter, sort, select, actions). --}}
    @php
        $statusNames = ['Active', 'Pending approval', 'Draft', 'Paused', 'Completed'];
        $campaignStatusCounts = collect($campaigns)->countBy('status')->all();
    @endphp
    <x-card :title="__('Recent campaigns')" :subtitle="__('Budget, delivery and return for the latest campaigns in this workspace')" class="mt-4">
        <x-table.root id="campaigns-table" client>
            <div class="flex flex-wrap items-center gap-2">
                <x-table.search client :placeholder="__('Search campaigns or clients')" class="w-full sm:w-64" />
                <x-table.filter client name="status" :label="__('Status')" :options="array_combine($statusNames, array_map('__', $statusNames))" :counts="$campaignStatusCounts" />
                <x-table.filter client name="objective" :label="__('Objective')" :options="['Awareness' => __('Awareness'), 'Engagement' => __('Engagement'), 'Conversions' => __('Conversions')]"
                    :counts="collect($campaigns)->countBy('objective')->all()" />
            </div>

            <x-table.toolbar>
                <span><b class="num font-semibold text-fg" data-table-count>{{ count($campaigns) }}</b> {{ __('campaigns') }}</span>
                <button type="button" data-table-reset hidden class="rounded-md px-1.5 py-1 text-xs font-semibold text-primary-ink hover:underline">{{ __('Reset') }}</button>

                <x-slot:bulk>
                    <x-button size="sm" variant="ghost" icon="circle-pause" data-toast-demo='{"type":"info","title":"Campaigns paused","text":"Hook this button up to your own endpoint."}'>{{ __('Pause') }}</x-button>
                    <x-button size="sm" variant="ghost" icon="download" data-toast-demo='{"type":"success","title":"Export started","text":"Hook this button up to your own endpoint."}'>{{ __('Export') }}</x-button>
                    <x-button size="sm" variant="ghost" icon="trash-2" class="!text-error hover:!bg-error/10" data-table-remove-selected="campaigns-table">{{ __('Delete') }}</x-button>
                </x-slot:bulk>
            </x-table.toolbar>

            <x-table min-width="1040px" client>
                <x-slot:head>
                    <x-table.select-all />
                    <x-table.th column="name">{{ __('Campaign') }}</x-table.th>
                    <x-table.th column="client">{{ __('Client') }}</x-table.th>
                    <x-table.th>{{ __('Influencers') }}</x-table.th>
                    <x-table.th align="right" column="reach">{{ __('Reach') }}</x-table.th>
                    <x-table.th align="right" column="er" title="{{ __('Engagement rate') }}">{{ __('ER') }}</x-table.th>
                    <x-table.th align="right" column="budget">{{ __('Budget') }}</x-table.th>
                    <x-table.th column="spent">{{ __('Spent') }}</x-table.th>
                    <x-table.th align="right" column="roi">{{ __('ROI') }}</x-table.th>
                    <x-table.th column="status">{{ __('Status') }}</x-table.th>
                    <x-table.th><span class="sr-only">{{ __('Actions') }}</span></x-table.th>
                </x-slot:head>

                @foreach ($campaigns as $campaign)
                    @php($spentPct = $campaign['budget'] ? $campaign['spent'] / $campaign['budget'] * 100 : 0)
                    <tr id="campaign-{{ $campaign['id'] }}"
                        data-search="{{ $campaign['name'] }} {{ $campaign['client'] }} {{ collect($campaign['influencers'])->pluck('name')->implode(' ') }}"
                        data-filter-status="{{ $campaign['status'] }}" data-filter-objective="{{ $campaign['objective'] }}"
                        data-sort-name="{{ $campaign['name'] }}" data-sort-client="{{ $campaign['client'] }}" data-sort-reach="{{ $campaign['reach'] ?? 0 }}"
                        data-sort-er="{{ $campaign['engagement_rate'] ?? 0 }}" data-sort-budget="{{ $campaign['budget'] }}" data-sort-spent="{{ $campaign['spent'] }}"
                        data-sort-roi="{{ $campaign['roi'] ?? -1 }}" data-sort-status="{{ array_search($campaign['status'], $statusNames) }}">
                        <x-table.select :value="$campaign['id']" :label="__('Select :name', ['name' => $campaign['name']])" />
                        <td>
                            <span class="block max-w-[250px] truncate font-semibold text-fg">{{ $campaign['name'] }}</span>
                            <span class="mt-0.5 flex items-center gap-1.5 text-xs text-fg-3">
                                <span class="inline-flex" title="{{ $campaign['objective'] }}"><x-icon :name="$objectiveIcons[$campaign['objective']]" class="size-3.5" /><span class="sr-only">{{ $campaign['objective'] }},</span></span>
                                <span class="num whitespace-nowrap">{{ $campaign['dates'] }}</span>
                            </span>
                        </td>
                        <td>
                            <span class="flex max-w-[160px] items-center gap-2">
                                <x-avatar :name="$campaign['client']" :tile="$campaign['client_tile']" size="xs" square />
                                <span class="truncate text-fg-2">{{ $campaign['client'] }}</span>
                            </span>
                        </td>
                        <td><x-avatar-stack :people="$campaign['influencers']" /></td>
                        <td class="num text-right font-medium">{{ $campaign['reach'] ? Number::abbreviate($campaign['reach'], maxPrecision: 1) : '—' }}</td>
                        <td class="num text-right font-medium">{{ $campaign['engagement_rate'] ? number_format($campaign['engagement_rate'], 1).'%' : '—' }}</td>
                        <td class="num text-right font-medium">{{ $money($campaign['budget']) }}</td>
                        <td>
                            <div class="w-[112px]">
                                <div class="flex justify-between gap-2 text-xs">
                                    <span class="num font-semibold text-fg">{{ $money($campaign['spent']) }}</span>
                                    <span class="num text-fg-3">{{ round($spentPct) }}%</span>
                                </div>
                                <x-progress :value="$spentPct" :tone="$spentPct >= 90 && $campaign['status'] === 'Active' ? 'warning' : 'primary'" height="h-1" class="mt-1.5"
                                    :label="__(':pct% of budget spent', ['pct' => round($spentPct)])" />
                            </div>
                        </td>
                        <td class="num text-right font-semibold">{{ $campaign['roi'] !== null ? $campaign['roi'].'%' : '—' }}</td>
                        <td><x-status-badge :status="$campaign['status']" /></td>
                        <x-table.row-actions :label="__('Actions for :name', ['name' => $campaign['name']])">
                            <x-dropdown-item icon="eye" data-dialog-open="campaign-view" :data-dialog-fill="json_encode([
                                'name' => $campaign['name'],
                                'client' => $campaign['client'].' · '.$campaign['dates'],
                                'reach' => $campaign['reach'] ? Number::abbreviate($campaign['reach'], maxPrecision: 1) : '—',
                                'roi' => $campaign['roi'] !== null ? $campaign['roi'].'%' : '—',
                                'budget' => $money($campaign['spent']).' of '.$money($campaign['budget']),
                                'status' => $campaign['status'],
                            ])">{{ __('View details') }}</x-dropdown-item>
                            <x-dropdown-item icon="pencil" data-toast-demo='{"type":"info","title":"Edit campaign","text":"Wire this to your campaign form."}'>{{ __('Edit campaign') }}</x-dropdown-item>
                            <x-dropdown-item icon="copy" data-toast-demo='{"type":"success","title":"Campaign duplicated"}'>{{ __('Duplicate') }}</x-dropdown-item>
                            @if (in_array($campaign['status'], ['Active', 'Paused'], true))
                                <x-dropdown-item :icon="$campaign['status'] === 'Active' ? 'circle-pause' : 'circle-play'"
                                    :data-toast-demo="json_encode(['type' => 'info', 'title' => $campaign['status'] === 'Active' ? __('Campaign paused') : __('Campaign resumed')])">
                                    {{ $campaign['status'] === 'Active' ? __('Pause campaign') : __('Resume campaign') }}
                                </x-dropdown-item>
                            @endif
                            <x-dropdown-separator />
                            <x-dropdown-item icon="trash-2" danger data-table-remove-row="campaign-{{ $campaign['id'] }}" data-toast="{{ __(':name deleted', ['name' => $campaign['name']]) }}">{{ __('Delete campaign') }}</x-dropdown-item>
                        </x-table.row-actions>
                    </tr>
                @endforeach

                <x-table.empty client :colspan="11" icon="search-x" :title="__('No campaigns match')" :text="__('Try a different search term or remove a filter.')">
                    <x-button size="sm" data-table-reset>{{ __('Reset filters') }}</x-button>
                </x-table.empty>
            </x-table>
        </x-table.root>
    </x-card>

    <x-drawer id="campaign-view" :title="__('Campaign details')">
        <div class="space-y-5 p-4 sm:p-5">
            <div>
                <p class="text-lg font-semibold" data-fill="name"></p>
                <p class="text-[13px] text-fg-2" data-fill="client"></p>
            </div>
            <dl class="grid grid-cols-2 gap-2">
                <div class="rounded-lg bg-surface-2 p-3"><dt class="text-xs font-medium text-fg-2">{{ __('Reach') }}</dt><dd class="num mt-1 text-base font-bold" data-fill="reach"></dd></div>
                <div class="rounded-lg bg-surface-2 p-3"><dt class="text-xs font-medium text-fg-2">{{ __('ROI') }}</dt><dd class="num mt-1 text-base font-bold" data-fill="roi"></dd></div>
                <div class="rounded-lg bg-surface-2 p-3"><dt class="text-xs font-medium text-fg-2">{{ __('Spent') }}</dt><dd class="num mt-1 text-sm font-semibold" data-fill="budget"></dd></div>
                <div class="rounded-lg bg-surface-2 p-3"><dt class="text-xs font-medium text-fg-2">{{ __('Status') }}</dt><dd class="mt-1 text-sm font-semibold" data-fill="status"></dd></div>
            </dl>
        </div>
        <x-slot:footer>
            <x-button class="flex-1" data-dialog-close>{{ __('Close') }}</x-button>
        </x-slot:footer>
    </x-drawer>

    <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
        {{-- ROI --}}
        <x-card :title="__('ROI analytics')" :subtitle="__('Attributed through creator links and promo codes')">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <p class="text-[13px] font-medium text-fg-2">{{ __('Overall ROI') }}</p>
                    <p class="mt-0.5 flex items-center gap-2">
                        <span class="num text-[32px] font-bold leading-10 tracking-tight">{{ $roi['roi'] }}%</span>
                        <x-change :current="$roi['roi']" :previous="$roi['previous']" points />
                    </p>
                </div>
                <p class="max-w-[190px] text-right text-xs text-fg-2">
                    {!! __('Every :one spent returned :amount in attributed revenue', ['one' => '<b class="font-semibold text-fg">$1</b>', 'amount' => '<b class="num font-semibold text-fg">$'.number_format($roiMultiple, 2).'</b>']) !!}
                </p>
            </div>

            <div class="mt-4 space-y-3">
                <div>
                    <div class="flex justify-between text-xs"><span class="flex items-center gap-1.5 font-medium text-fg-2"><span class="size-2 rounded-full bg-c1"></span>{{ __('Attributed revenue') }}</span><b class="num font-semibold">{{ $money($roi['revenue']) }}</b></div>
                    <div class="mt-1.5 h-2.5 rounded-full bg-c1"></div>
                </div>
                <div>
                    <div class="flex justify-between text-xs"><span class="flex items-center gap-1.5 font-medium text-fg-2"><span class="size-2 rounded-full bg-c3"></span>{{ __('Campaign spend') }}</span><b class="num font-semibold">{{ $money($roi['spend']) }}</b></div>
                    <div class="mt-1.5 h-2.5 rounded-full bg-surface-3"><div class="h-full rounded-full bg-c3" style="width: {{ $roi['spend'] / $roi['revenue'] * 100 }}%"></div></div>
                </div>
            </div>

            <dl class="mt-4 grid grid-cols-3 gap-2">
                <x-stat variant="tile" :label="__('CPA')" :hint="__('Cost per acquisition: spend divided by attributed conversions')" :value="'$'.number_format($costPerAcquisition, 2)">
                    <x-change :current="$costPerAcquisition" :previous="5.04" invert />
                </x-stat>
                <x-stat variant="tile" :label="__('CPE')" :hint="__('Cost per engagement: spend divided by likes, comments, shares and saves')" :value="'$'.number_format($costPerEngagement, 2)">
                    <x-change :current="$costPerEngagement" :previous="0.15" invert />
                </x-stat>
                <x-stat variant="tile" :label="__('CVR')" :hint="__('Conversion rate: conversions divided by engagements')" :value="number_format($conversionRate, 2).'%'">
                    <x-change :current="$conversionRate" :previous="2.71" points />
                </x-stat>
            </dl>

            <div class="mt-5">
                <p class="mb-2 text-[13px] font-semibold">{{ __('ROI by month') }}</p>
                <x-mini-line-chart :values="array_column($roi['monthly'], 'roi')" :labels="array_column($roi['monthly'], 'month')" suffix="%" />
            </div>
        </x-card>

        {{-- Funnel --}}
        <x-card :title="__('Campaign funnel')" :subtitle="__('Creator pipeline, year to date')">
            <x-funnel :steps="$funnel" />
            <div class="mt-3 flex items-center justify-between gap-3 rounded-lg bg-success/10 px-3 py-2.5 ring-1 ring-inset ring-success/20">
                <span class="flex items-center gap-2 text-[13px] font-semibold text-success"><x-icon name="shopping-bag" />{{ __('Conversions') }}</span>
                <span class="text-right">
                    <b class="num block text-base font-bold">{{ number_format($roi['conversions']) }}</b>
                    <span class="num block text-xs text-fg-2">{{ __(':count per published post', ['count' => round($roi['conversions'] / end($funnel)['value'])]) }}</span>
                </span>
            </div>
        </x-card>

        {{-- Status overview --}}
        <x-card :title="__('Campaign status')" :subtitle="__('Across every client in this workspace')" class="md:col-span-2 xl:col-span-1">
            <div class="flex items-baseline gap-2">
                <span class="num text-[32px] font-bold leading-10 tracking-tight">{{ $statusTotal }}</span>
                <span class="text-[13px] text-fg-2">{{ __('campaigns in total') }}</span>
            </div>
            <x-stack-bar class="mt-3" height="h-3" :label="__('Campaigns by status')"
                :segments="collect($statusOverview)->map(fn ($row) => ['label' => $row['status'], 'value' => $row['count'], 'color' => $statusColors[$row['status']]])->all()" />
            <ul class="-mx-2 mt-4 space-y-0.5">
                @foreach ($statusOverview as $row)
                    <li class="flex items-center gap-3 rounded-lg px-2 py-2">
                        <x-status-badge :status="$row['status']" />
                        <span class="flex-1"></span>
                        <b class="num text-sm font-semibold">{{ $row['count'] }}</b>
                        <span class="num w-12 text-right text-xs text-fg-3">{{ number_format($row['count'] / $statusTotal * 100, 1) }}%</span>
                    </li>
                @endforeach
            </ul>
            <x-alert tone="info" icon="clock-3" class="mt-3 py-2.5 text-xs">
                {!! __(':count campaigns are waiting for client approval.', ['count' => '<b class="font-semibold">9</b>']) !!}
            </x-alert>
        </x-card>
    </div>

    <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
        {{-- Top influencers --}}
        <x-card :title="__('Top influencers')" :subtitle="__('Ranked by engagement growth this period')">
            <ol class="-mx-2 space-y-0.5">
                @foreach ($topInfluencers as $influencer)
                    <li class="flex items-center gap-3 rounded-lg px-2 py-2 transition-colors hover:bg-surface-2">
                        <span class="num w-3 shrink-0 text-xs font-semibold text-fg-3">{{ $loop->iteration }}</span>
                        <x-avatar :name="$influencer['name']" :tile="$influencer['tile']" ring />
                        <div class="min-w-0 flex-1">
                            <p class="flex items-center gap-1"><span class="truncate text-sm font-semibold">{{ $influencer['name'] }}</span><x-verified size="size-3.5" /></p>
                            <p class="truncate text-xs text-fg-3">{{ '@'.$influencer['handle'] }} · {{ $influencer['category'] }}</p>
                        </div>
                        <div class="text-right">
                            <p class="num text-sm font-semibold">{{ Number::abbreviate($influencer['followers'], maxPrecision: 1) }}</p>
                            <p @class(['num inline-flex items-center text-[11px] font-semibold', 'text-success' => $influencer['trend'] >= 0, 'text-error' => $influencer['trend'] < 0])>
                                <x-icon :name="$influencer['trend'] >= 0 ? 'arrow-up-right' : 'arrow-down-right'" class="size-3" stroke-width="2.25" />{{ number_format(abs($influencer['trend']), 1) }}%
                            </p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </x-card>

        {{-- Activity --}}
        <x-card :title="__('Recent activity')" :subtitle="__('What changed across your workspace')">
            <x-timeline>
                @foreach ($activity as $event)
                    <x-timeline-item :icon="$event['icon']" :tone="$event['tone']" :time="$event['time']">
                        <b>{{ $event['actor'] }}</b> {{ $event['action'] }} @if ($event['subject'])<b>{{ $event['subject'] }}</b>@endif
                    </x-timeline-item>
                @endforeach
            </x-timeline>
        </x-card>

        {{-- Messages --}}
        <x-card :title="__('Messages')" :subtitle="trans_choice(':count unread conversation|:count unread conversations', $unreadThreads)" class="md:col-span-2 xl:col-span-1">
            <div class="-mx-2 space-y-0.5">
                @foreach ($threads as $thread)
                    <x-dashboard.thread-row :name="$thread['name']" :tile="$thread['tile']" :message="$thread['message']" :time="$thread['time']"
                        :unread="$thread['unread']" :online="$thread['online']" :mine="$thread['mine']" />
                @endforeach
            </div>
        </x-card>
    </div>

    <p class="mt-8 text-center text-xs text-fg-3">{{ __('Sample data. Replace the methods in DashboardController with your own queries.') }}</p>
</x-layouts.app>
