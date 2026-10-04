@php
    use Illuminate\Support\Number;

    $sections = [
        'buttons' => 'Buttons',
        'badges' => 'Badges & chips',
        'avatars' => 'Avatars',
        'forms' => 'Forms',
        'navigation' => 'Tabs & menus',
        'overlays' => 'Overlays',
        'feedback' => 'Feedback',
        'data' => 'Data display',
        'charts' => 'Charts',
        'cards' => 'Domain cards',
        'appearance' => 'Appearance',
    ];

    $people = [
        ['name' => 'Yasmine Belkacem', 'tile' => 'coral'],
        ['name' => 'Karim Haddad', 'tile' => 'amber'],
        ['name' => 'Nour Hamidi', 'tile' => 'sky'],
        ['name' => 'Lina Saïdi', 'tile' => 'pink'],
        ['name' => 'Amine Tech', 'tile' => 'teal'],
    ];

    $chartLabels = collect(range(29, 0))->map(fn ($daysAgo) => now()->subDays($daysAgo)->toDateString())->all();
    $wave = fn (int $base, float $amplitude, float $phase) => collect(range(0, 29))->map(fn ($i) => (int) round($base + sin($i / 3 + $phase) * $amplitude + $i * $base / 80))->all();

    $influencer = [
        'name' => 'Karim Haddad', 'handle' => 'karimcooks', 'platform' => 'tiktok', 'tile' => 'amber', 'category' => 'Food',
        'location' => 'Oran, Algeria', 'verified' => true, 'followers' => 864_000, 'engagement_rate' => 8.4, 'avg_views' => 1_100_000,
        'reach' => 702_000, 'female' => 54, 'age' => '18–24', 'languages' => ['Arabic', 'French'], 'rate' => 1_400,
    ];

    $post = [
        'title' => 'Chakhchoukha in 60 seconds', 'type' => 'TikTok', 'platform' => 'tiktok', 'icon' => 'utensils', 'tile' => 'amber',
        'duration' => '0:58', 'views' => 2_140_000, 'likes' => 264_000, 'comments' => 6_100, 'shares' => 18_400, 'saves' => 22_300,
        'engagement_rate' => 11.2, 'creator' => ['name' => 'Karim Haddad', 'tile' => 'amber'],
    ];

    $campaign = [
        'name' => '5G Launch Wave', 'client' => 'Atlas Telecom', 'client_tile' => 'violet', 'status' => 'Active',
        'budget' => 32_000, 'spent' => 14_200, 'reach' => 4_100_000, 'engagement_rate' => 3.9, 'roi' => 215, 'influencers' => $people,
    ];
@endphp

<x-layouts.app :title="__('Components')">
    <x-page-header :title="__('Components')"
        :description="__('Every building block from the template as a reusable Blade component. Copy the markup from resources/views/ui-kit.blade.php.')" />

    <nav aria-label="{{ __('Component sections') }}" class="mt-5 flex flex-wrap gap-2">
        @foreach ($sections as $anchor => $label)
            <a href="#{{ $anchor }}" class="inline-flex h-8 items-center rounded-lg border border-line bg-surface px-3 text-[13px] font-semibold text-fg-2 shadow-card transition-colors hover:border-line-strong hover:text-fg">{{ $label }}</a>
        @endforeach
    </nav>

    <div class="mt-6 space-y-4">
        {{-- Buttons --}}
        <x-ui-kit.section id="buttons" title="Buttons" :tags="['button', 'icon-button', 'quick-action']">
            <x-ui-kit.example label="Variants">
                <x-button variant="primary" icon="plus">Primary</x-button>
                <x-button icon="download">Secondary</x-button>
                <x-button variant="soft" icon="sparkles">Soft</x-button>
                <x-button variant="ghost" icon-right="arrow-up-right">Ghost</x-button>
                <x-button variant="danger" icon="trash-2">Danger</x-button>
                <x-button variant="primary" disabled>Disabled</x-button>
            </x-ui-kit.example>
            <x-ui-kit.example label="Sizes">
                <x-button variant="primary" size="sm">Small</x-button>
                <x-button variant="primary">Medium</x-button>
                <x-button variant="primary" size="lg">Large</x-button>
                <x-button :href="route('dashboard')" icon="layout-dashboard">Link button</x-button>
            </x-ui-kit.example>
            <x-ui-kit.example label="Icon buttons">
                <x-icon-button icon="bell" label="Notifications" />
                <x-icon-button icon="settings" label="Settings" />
                <x-icon-button icon="ellipsis" label="More" size="sm" />
            </x-ui-kit.example>
            <x-ui-kit.example label="Quick actions" class="grid max-w-md grid-cols-3 gap-2">
                <x-quick-action icon="megaphone">Create campaign</x-quick-action>
                <x-quick-action icon="compass">Find influencers</x-quick-action>
                <x-quick-action icon="user-plus">Invite influencers</x-quick-action>
                <x-quick-action icon="file-chart-column">Create report</x-quick-action>
                <x-quick-action icon="send">Send message</x-quick-action>
                <x-quick-action icon="download">Export analytics</x-quick-action>
            </x-ui-kit.example>
        </x-ui-kit.section>

        {{-- Badges --}}
        <x-ui-kit.section id="badges" title="Badges & chips" :tags="['badge', 'status-badge', 'change', 'kbd', 'platform-chip', 'verified', 'filter-chip']">
            <x-ui-kit.example label="Badge tones">
                @foreach (['success', 'warning', 'error', 'info', 'primary', 'secondary', 'neutral'] as $tone)
                    <x-badge :tone="$tone" dot>{{ ucfirst($tone) }}</x-badge>
                @endforeach
                <x-badge tone="primary" icon="sparkles">With icon</x-badge>
            </x-ui-kit.example>
            <x-ui-kit.example label="Campaign status">
                @foreach (['Active', 'Pending approval', 'Draft', 'Paused', 'Completed'] as $status)
                    <x-status-badge :status="$status" />
                @endforeach
            </x-ui-kit.example>
            <x-ui-kit.example label="Change indicators">
                <x-change :current="120" :previous="100" />
                <x-change :current="90" :previous="100" />
                <x-change :current="4.87" :previous="4.27" points />
                <x-change :current="110" :previous="100" neutral />
                <x-change :current="4.2" :previous="5.04" invert />
            </x-ui-kit.example>
            <x-ui-kit.example label="Chips">
                <x-platform-chip platform="instagram" />
                <x-platform-chip platform="tiktok" />
                <x-platform-chip platform="youtube" />
                <x-platform-chip platform="x" compact />
                <x-verified />
                <x-kbd>Ctrl K</x-kbd>
                <x-filter-chip>Platform</x-filter-chip>
                <x-filter-chip :count="2">Category</x-filter-chip>
            </x-ui-kit.example>
        </x-ui-kit.section>

        {{-- Avatars --}}
        <x-ui-kit.section id="avatars" title="Avatars" :tags="['avatar', 'avatar-stack', 'icon-tile']">
            <x-ui-kit.example label="Sizes & shapes">
                @foreach (['xs', 'sm', 'md', 'lg', 'xl'] as $size)
                    <x-avatar name="Sarah Benali" :size="$size" />
                @endforeach
                <x-avatar name="Atlas Telecom" tile="violet" square />
                <x-avatar name="Karim Haddad" tile="amber" online />
                <x-avatar name="Yasmine Belkacem" tile="coral" size="lg" ring />
            </x-ui-kit.example>
            <x-ui-kit.example label="Stacks">
                <x-avatar-stack :people="$people" />
                <x-avatar-stack :people="$people" size="sm" :max="4" />
            </x-ui-kit.example>
            <x-ui-kit.example label="Icon tiles">
                @foreach (['primary', 'success', 'info', 'warning', 'error', 'secondary', 'neutral'] as $tone)
                    <x-icon-tile icon="megaphone" :tone="$tone" />
                @endforeach
                <x-icon-tile icon="heart" tone="secondary" round />
                <x-icon-tile icon="trash-2" tone="error" size="lg" />
            </x-ui-kit.example>
        </x-ui-kit.section>

        {{-- Forms --}}
        <x-ui-kit.section id="forms" title="Forms" :tags="['field', 'select', 'textarea', 'checkbox', 'switch', 'search-input', 'field-shell']">
            <div class="grid gap-5 md:grid-cols-2">
                <x-field name="demo_email" type="email" label="Email" icon="mail" placeholder="you@company.com" />
                <x-field name="demo_budget" type="number" label="Budget" prefix="$" placeholder="5,000" hint="Total across all creators." />
                <x-field name="demo_password" type="password" label="Password" icon="lock" />
                <x-field name="demo_role" label="Role" value="Head of Influence" disabled hint="Managed by your workspace admin." />
                <x-select name="demo_objective" label="Objective" placeholder="Choose an objective"
                    :options="['awareness' => 'Awareness', 'engagement' => 'Engagement', 'conversions' => 'Conversions']" />
                <x-select name="demo_timezone" label="Time zone" optional :options="['Africa/Algiers' => 'Africa/Algiers (CET)', 'Europe/Paris' => 'Europe/Paris (CEST)']" value="Africa/Algiers" />
                <x-textarea name="demo_brief" label="Creative brief" placeholder="What should creators know?" class="md:col-span-2" />
                <x-field-shell for="demo_invalid" label="Field with an error" error="This handle is already taken.">
                    <input id="demo_invalid" aria-invalid="true" value="@karimcooks" class="form-control h-10 px-3" />
                </x-field-shell>
                <div class="space-y-4">
                    <x-search-input name="demo_q" placeholder="Search campaigns" />
                    <x-checkbox name="demo_remember" label="Keep me signed in" checked />
                </div>
            </div>
            <div class="divide-y divide-line rounded-lg border border-line px-4 [&>label]:py-3.5">
                <x-switch name="demo_campaign_updates" label="Campaign updates" description="Milestones, status changes and end-of-campaign results" checked />
                <x-switch name="demo_weekly" label="Weekly summary email" description="Every Monday at 09:00" />
                <x-switch name="demo_security" label="Security alerts" description="New sign-ins and permission changes. Always on." checked disabled />
            </div>
        </x-ui-kit.section>

        {{-- Navigation --}}
        <x-ui-kit.section id="navigation" title="Tabs & menus" :tags="['tabs', 'tab', 'tab-panel', 'dropdown', 'dropdown-item', 'dropdown-header', 'dropdown-separator', 'date-range-picker']">
            <x-ui-kit.example label="Client-side tabs" class="block">
                <x-tabs label="Audience platform">
                    <x-tab target="tab-all" active icon="layers">All</x-tab>
                    <x-tab target="tab-instagram" icon="camera" :count="3">Instagram</x-tab>
                    <x-tab target="tab-tiktok" icon="music-2">TikTok</x-tab>
                </x-tabs>
                <x-tab-panel id="tab-all" active class="mt-3 text-[13px] text-fg-2">Showing every platform. Use ← → to move between tabs.</x-tab-panel>
                <x-tab-panel id="tab-instagram" class="mt-3 text-[13px] text-fg-2">Instagram panel content.</x-tab-panel>
                <x-tab-panel id="tab-tiktok" class="mt-3 text-[13px] text-fg-2">TikTok panel content.</x-tab-panel>
            </x-ui-kit.example>
            <x-ui-kit.example label="Link tabs (server-side)">
                <x-tabs label="Period">
                    <x-tab :href="request()->url().'#navigation'" active>7 days</x-tab>
                    <x-tab :href="request()->url().'#navigation'">30 days</x-tab>
                    <x-tab :href="request()->url().'#navigation'" size="md">90 days</x-tab>
                </x-tabs>
            </x-ui-kit.example>
            <x-ui-kit.example label="Dropdown & date range">
                <x-dropdown align="start" width="w-56" label="Campaign actions">
                    <x-slot:trigger>
                        <x-button data-dropdown-trigger aria-haspopup="menu" aria-expanded="false" icon-right="chevron-down">Actions</x-button>
                    </x-slot:trigger>
                    <div class="p-1.5">
                        <x-dropdown-header>Campaign</x-dropdown-header>
                        <x-dropdown-item icon="eye">View details</x-dropdown-item>
                        <x-dropdown-item icon="pencil">Edit campaign</x-dropdown-item>
                        <x-dropdown-item icon="copy">Duplicate</x-dropdown-item>
                        <x-dropdown-separator />
                        <x-dropdown-item icon="trash-2" danger>Delete campaign</x-dropdown-item>
                    </div>
                </x-dropdown>
                <x-date-range-picker align="start" :presets="['7' => 'Last 7 days', '30' => 'Last 30 days', '90' => 'Last 90 days']" selected="30" :action="route('dashboard')" />
            </x-ui-kit.example>
        </x-ui-kit.section>

        {{-- Overlays --}}
        <x-ui-kit.section id="overlays" title="Overlays" :tags="['modal', 'drawer', 'toast', 'command-palette']">
            <x-ui-kit.example label="Dialogs">
                <x-button data-dialog-open="demo-modal" icon="square-pen">Open modal</x-button>
                <x-button data-dialog-open="demo-confirm" variant="danger" icon="trash-2">Confirm dialog</x-button>
                <x-button data-dialog-open="demo-drawer" icon="panel-right">Open drawer</x-button>
                <x-button data-palette-open icon="search">Command palette</x-button>
            </x-ui-kit.example>
            <x-ui-kit.example label="Toasts (window.toast)">
                <x-button size="sm" data-toast-demo='{"type":"success","title":"Campaign created","text":"Autumn Coffee Rituals is saved as a draft."}'>Success</x-button>
                <x-button size="sm" data-toast-demo='{"type":"info","title":"Sync started","text":"Stats will refresh in a minute."}'>Info</x-button>
                <x-button size="sm" data-toast-demo='{"type":"warning","title":"Budget almost spent","text":"Back to School Bundle is at 92%."}'>Warning</x-button>
                <x-button size="sm" data-toast-demo='{"type":"error","title":"Payment failed","text":"The card on file was declined."}'>Error</x-button>
            </x-ui-kit.example>
            <x-ui-kit.example label="Tooltip">
                <x-button data-tip="Tooltips work on any element with data-tip">Hover me</x-button>
            </x-ui-kit.example>
        </x-ui-kit.section>

        {{-- Feedback --}}
        <x-ui-kit.section id="feedback" title="Feedback" :tags="['alert', 'toast', 'empty-state', 'error-state', 'skeleton', 'skeleton-card', 'progress']">
            <div class="grid gap-3 md:grid-cols-2">
                <x-alert>Profile saved.</x-alert>
                <x-alert tone="info">9 campaigns are waiting for client approval.</x-alert>
                <x-alert tone="warning">YouTube stats paused for 1 account.</x-alert>
                <x-alert tone="error">We couldn't reach the payment provider.</x-alert>
            </div>
            <div class="flex flex-col items-center gap-2 rounded-xl bg-surface-2 p-4 sm:items-start">
                <x-toast title="Campaign created" text="Autumn Coffee Rituals is saved as a draft." />
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <div class="rounded-lg border border-dashed border-line">
                    <x-empty-state icon="search-x" title="No campaigns match" text="Try a different search term or remove a filter.">
                        <x-button size="sm">Reset filters</x-button>
                    </x-empty-state>
                </div>
                <x-error-state title="YouTube stats are paused" text="Access for @aminetech expired. Reconnect the account to resume syncing.">
                    <x-button size="sm" variant="primary" icon="refresh-cw">Reconnect</x-button>
                </x-error-state>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <x-skeleton-card class="h-56" />
                <div class="space-y-4 rounded-xl border border-line p-5">
                    <x-progress :value="64" label="Primary" />
                    <x-progress :value="92" tone="warning" label="Warning" />
                    <x-progress :value="40" tone="success" height="h-2.5" :marker="76" label="With marker" />
                    <x-progress :value="72" tone="brand" height="h-2" label="Brand" />
                    <div class="flex items-center gap-3">
                        <x-skeleton class="size-10 rounded-full" />
                        <div class="flex-1 space-y-2"><x-skeleton class="h-3 w-1/2" /><x-skeleton class="h-3 w-1/3" /></div>
                    </div>
                </div>
            </div>
        </x-ui-kit.section>

        {{-- Data display --}}
        <x-ui-kit.section id="data" title="Data display" :tags="['card', 'stat', 'table', 'table.root', 'table.th', 'table.search', 'table.filter', 'table.toolbar', 'table.select-all', 'table.select', 'table.row-actions', 'table.per-page', 'table.empty', 'timeline', 'timeline-item', 'notification', 'page-header']">
            <dl class="grid grid-cols-2 gap-3 lg:grid-cols-4">
                <x-stat label="Reach" value="1.7M"><x-change :current="118" :previous="100" /></x-stat>
                <x-stat label="Conversions" value="2,397"><x-change :current="126" :previous="100" /></x-stat>
                <x-stat variant="tile" label="CPA" value="$4.49" hint="Cost per acquisition"><x-change :current="4.49" :previous="5.04" invert /></x-stat>
                <x-stat variant="tile" dot="bg-primary" label="Spent" value="$82,650" />
            </dl>

            <p class="text-[13px] text-fg-2">{{ __('Full examples: the Users page (server-side: search, filter, sort, bulk delete, CSV export, row actions) and the dashboard campaigns table (in-browser).') }}</p>
            <x-table min-width="560px" sort="name" direction="asc">
                <x-slot:head>
                    <x-table.th column="name">Creator</x-table.th>
                    <x-table.th>Platform</x-table.th>
                    <x-table.th align="right" column="followers">Followers</x-table.th>
                    <x-table.th>Status</x-table.th>
                </x-slot:head>
                @foreach (array_slice($people, 0, 3) as $index => $person)
                    <tr>
                        <td><span class="flex items-center gap-2"><x-avatar :name="$person['name']" :tile="$person['tile']" size="xs" /><span class="font-semibold">{{ $person['name'] }}</span></span></td>
                        <td><x-platform-chip :platform="['instagram', 'tiktok', 'youtube'][$index]" /></td>
                        <td class="num text-right font-medium">{{ ['1.2M', '864K', '2.4M'][$index] }}</td>
                        <td><x-status-badge :status="['Active', 'Paused', 'Completed'][$index]" /></td>
                    </tr>
                @endforeach
            </x-table>

            <div class="grid gap-4 md:grid-cols-2">
                <x-timeline>
                    <x-timeline-item icon="megaphone" tone="primary" time="12 min ago"><b>You</b> created <b>Autumn Coffee Rituals</b><x-slot:meta><x-badge>Draft</x-badge></x-slot:meta></x-timeline-item>
                    <x-timeline-item icon="user-round-check" tone="success" time="38 min ago"><b>Karim Haddad</b> accepted your invitation</x-timeline-item>
                    <x-timeline-item icon="credit-card" tone="success" time="3 h ago">Payment of <b>$2,400</b> completed<x-slot:meta><x-badge tone="success">Paid</x-badge></x-slot:meta></x-timeline-item>
                </x-timeline>
                <div class="overflow-hidden rounded-xl border border-line">
                    <x-notification icon="file-check-2" tone="info" title="Content awaiting approval" time="1 h ago" unread>
                        Lina Saïdi submitted 3 posts for Back to School Bundle.
                        <x-slot:actions><x-button size="sm" variant="primary">Approve all</x-button><x-button size="sm">Review</x-button></x-slot:actions>
                    </x-notification>
                    <x-notification icon="credit-card" tone="success" title="Payment completed" time="3 h ago">$2,400 paid to Amine Tech.</x-notification>
                    <x-notification icon="triangle-alert" tone="warning" title="Reconnect a YouTube account" time="Yesterday">Stats stopped syncing on Sep 26.</x-notification>
                </div>
            </div>
        </x-ui-kit.section>

        {{-- Charts --}}
        <x-ui-kit.section id="charts" title="Charts" :tags="['line-chart', 'sparkline', 'mini-line-chart', 'bar-chart', 'bar-list', 'donut', 'stack-bar', 'funnel']">
            <x-line-chart :labels="$chartLabels" label="Demo line chart" :series="[
                ['key' => 'reach', 'label' => 'Reach', 'color' => 'c1', 'values' => $wave(52000, 9000, 0)],
                ['key' => 'engagement', 'label' => 'Engagement', 'color' => 'c2', 'values' => $wave(2400, 500, 1)],
                ['key' => 'conversions', 'label' => 'Conversions', 'color' => 'c3', 'values' => $wave(70, 18, 2)],
            ]" />
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                <div>
                    <p class="mb-3 text-[13px] font-semibold">Sparklines</p>
                    <div class="flex flex-wrap gap-4">
                        <x-sparkline :values="[3, 5, 4, 6, 7, 6, 9, 10]" />
                        <x-sparkline :values="[9, 8, 8, 6, 7, 5, 4, 3]" tone="error" />
                        <x-sparkline :values="[4, 6, 5, 7, 6, 8, 7, 9]" tone="primary" />
                    </div>
                    <p class="mb-2 mt-6 text-[13px] font-semibold">Mini line</p>
                    <x-mini-line-chart :values="[286, 298, 341, 322, 305, 312]" :labels="['Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep']" suffix="%" />
                </div>
                <div>
                    <p class="mb-3 text-[13px] font-semibold">Bar chart</p>
                    <x-bar-chart :items="[['label' => 'Apr', 'value' => 7260], ['label' => 'May', 'value' => 8410], ['label' => 'Jun', 'value' => 9120], ['label' => 'Jul', 'value' => 9270], ['label' => 'Aug', 'value' => 10010], ['label' => 'Sep', 'value' => 11240]]" />
                    <p class="mb-3 mt-6 text-[13px] font-semibold">Bar list</p>
                    <x-bar-list :items="[['label' => '13–17', 'value' => 6], ['label' => '18–24', 'value' => 38], ['label' => '25–34', 'value' => 34], ['label' => '35–44', 'value' => 15], ['label' => '45+', 'value' => 7]]" />
                </div>
                <div>
                    <p class="mb-3 text-[13px] font-semibold">Donut</p>
                    <div class="flex items-center gap-4">
                        <x-donut :size="112" :stroke="13" :segments="[['label' => 'Women', 'value' => 58, 'color' => 'c1'], ['label' => 'Men', 'value' => 39, 'color' => 'c2'], ['label' => 'Other', 'value' => 3, 'color' => 'c3']]">
                            <div><p class="num text-xl font-bold leading-6">58%</p><p class="text-xs text-fg-2">women</p></div>
                        </x-donut>
                        <ul class="min-w-0 flex-1 space-y-2.5 text-[13px]">
                            @foreach ([['Women', 58, 'bg-c1'], ['Men', 39, 'bg-c2'], ['Other', 3, 'bg-c3']] as [$label, $value, $dot])
                                <li class="flex items-center gap-2"><span class="size-2.5 rounded-full {{ $dot }}"></span><span class="flex-1 text-fg-2">{{ $label }}</span><b class="num font-semibold">{{ $value }}%</b></li>
                            @endforeach
                        </ul>
                    </div>
                    <p class="mb-3 mt-6 text-[13px] font-semibold">Stacked bar</p>
                    <x-stack-bar height="h-3" :segments="[['label' => 'Arabic', 'value' => 46, 'color' => 'c1'], ['label' => 'French', 'value' => 38, 'color' => 'c2'], ['label' => 'English', 'value' => 11, 'color' => 'c3'], ['label' => 'Tamazight', 'value' => 5, 'color' => 'info']]" />
                </div>
            </div>
            <div class="max-w-md">
                <p class="mb-3 text-[13px] font-semibold">Funnel</p>
                <x-funnel :steps="[['label' => 'Contacted', 'value' => 1240], ['label' => 'Interested', 'value' => 684], ['label' => 'Contracted', 'value' => 312], ['label' => 'Published', 'value' => 241]]" />
            </div>
        </x-ui-kit.section>

        {{-- Domain cards --}}
        <x-ui-kit.section id="cards" title="Domain cards" :tags="['dashboard.kpi-card', 'dashboard.influencer-card', 'dashboard.content-card', 'dashboard.campaign-card', 'dashboard.thread-row', 'chat-bubble']">
            <div class="grid grid-cols-2 gap-3 xl:grid-cols-4">
                <x-dashboard.kpi-card :kpi="['label' => 'Total reach', 'icon' => 'eye', 'value' => 12_800_000, 'previous' => 10_811_000, 'format' => 'compact', 'hint' => 'Unique accounts reached.']" />
                <x-dashboard.kpi-card :kpi="['label' => 'ROI', 'icon' => 'trending-up', 'value' => 312, 'previous' => 325, 'format' => 'int', 'hint' => 'Revenue minus spend, over spend.']" />
            </div>
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <x-dashboard.influencer-card :influencer="$influencer">
                    <x-slot:actions>
                        <x-button size="sm">View profile</x-button>
                        <x-button size="sm" variant="primary">Add</x-button>
                    </x-slot:actions>
                </x-dashboard.influencer-card>
                <x-dashboard.content-card :post="$post" />
                <div class="space-y-4 sm:col-span-2">
                    <x-dashboard.campaign-card :campaign="$campaign" />
                    <div class="-mx-2 rounded-xl border border-line p-2">
                        <x-dashboard.thread-row name="Karim Haddad" tile="amber" message="Draft is ready, sending the cut tonight 🎬" time="5 min" :unread="2" online />
                        <x-dashboard.thread-row name="Amine Tech" tile="teal" message="Paid this morning." time="Yesterday" mine />
                    </div>
                    <div class="flex flex-col gap-2 rounded-xl border border-line p-4">
                        <x-chat-bubble>Can we move the go-live for Skincare Science to Thursday?</x-chat-bubble>
                        <x-chat-bubble mine>Thursday works. I'll update the brief.</x-chat-bubble>
                    </div>
                </div>
            </div>
        </x-ui-kit.section>

        {{-- Appearance --}}
        <x-ui-kit.section id="appearance" title="Appearance" :tags="['theme-switch', 'theme-picker', 'layout-switch', 'brand']">
            <x-ui-kit.example label="Theme & layout switches">
                <x-theme-switch class="w-72" />
                <x-layout-switch class="w-56" />
                <x-brand />
            </x-ui-kit.example>
            <x-theme-picker class="max-w-lg" />
        </x-ui-kit.section>
    </div>

    <x-modal id="demo-modal" title="Invite influencers" description="They'll get an email with a link to join the campaign." icon="user-plus">
        <div class="space-y-4">
            <x-field name="invite_email" type="email" label="Email addresses" icon="mail" placeholder="creator@example.com" />
            <x-textarea name="invite_message" label="Message" optional rows="3" />
        </div>
        <x-slot:footer>
            <x-button data-dialog-close>Cancel</x-button>
            <x-button variant="primary" data-dialog-close data-toast-demo='{"type":"success","title":"Invitation sent"}'>Send invite</x-button>
        </x-slot:footer>
    </x-modal>

    <x-modal id="demo-confirm" title="Delete this campaign?" description="Ranked Night Series and its reports will be removed. This can't be undone." icon="trash-2" tone="error" size="sm:max-w-md">
        <x-slot:footer>
            <x-button data-dialog-close autofocus>Cancel</x-button>
            <x-button variant="danger" data-dialog-close data-toast-demo='{"type":"success","title":"Campaign deleted"}'>Delete</x-button>
        </x-slot:footer>
    </x-modal>

    <x-drawer id="demo-drawer" title="5G Launch Wave" subtitle="Atlas Telecom · Sep 10 – Nov 30">
        <div class="space-y-5 p-4 sm:p-5">
            <dl class="grid grid-cols-2 gap-2">
                <x-stat variant="tile" label="Reach" value="4.1M" />
                <x-stat variant="tile" label="ROI" value="215%" />
            </dl>
            <x-dashboard.campaign-card :campaign="$campaign" />
            <x-timeline>
                <x-timeline-item icon="megaphone" time="Sep 10">Campaign launched</x-timeline-item>
                <x-timeline-item icon="file-check-2" tone="info" time="Sep 18">3 posts approved</x-timeline-item>
            </x-timeline>
        </div>
        <x-slot:footer>
            <x-button class="flex-1" data-dialog-close>Close</x-button>
            <x-button class="flex-1" variant="primary" icon="pencil">Edit campaign</x-button>
        </x-slot:footer>
    </x-drawer>
</x-layouts.app>
