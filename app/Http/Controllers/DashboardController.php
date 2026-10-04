<?php

namespace App\Http\Controllers;

use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Random\Engine\Mt19937;
use Random\Randomizer;

/**
 * Renders the dashboard home page.
 *
 * Every figure below is deterministic sample data so the starter looks alive out of the box.
 * Replace each private method with real queries as your domain models land.
 */
class DashboardController extends Controller
{
    /**
     * The chart periods (in days) a visitor can switch between.
     *
     * @var list<int>
     */
    public const array PERIODS = [7, 30, 90];

    /**
     * Show the dashboard.
     */
    public function __invoke(Request $request): View
    {
        [$start, $end, $period] = $this->dateRange($request);

        return view('dashboard', [
            'period' => $period,
            'periods' => self::PERIODS,
            'from' => $period === null ? $start->toDateString() : null,
            'to' => $period === null ? $end->toDateString() : null,
            'kpis' => $this->kpis(),
            'performance' => $this->performance($start, $end),
            'roi' => $this->roi(),
            'funnel' => $this->funnel(),
            'threads' => $this->threads(),
            'budget' => $this->budget(),
            'campaigns' => $this->recentCampaigns(),
            'statusOverview' => $this->statusOverview(),
            'topInfluencers' => $this->topInfluencers(),
            'activity' => $this->activity(),
        ]);
    }

    /**
     * Resolve the reporting window: a custom ?from=&to= range (up to a year, not in the future),
     * otherwise one of the preset ?period= values, defaulting to 30 days.
     *
     * @return array{0: Carbon, 1: Carbon, 2: ?int}
     */
    private function dateRange(Request $request): array
    {
        $today = Carbon::today();

        try {
            $from = $request->filled('from') ? Carbon::parse($request->string('from'))->startOfDay() : null;
            $to = $request->filled('to') ? Carbon::parse($request->string('to'))->startOfDay() : null;
        } catch (InvalidFormatException) {
            $from = $to = null;
        }

        if ($from && $to && $from->lte($to) && $to->lte($today) && $from->diffInDays($to) < 366) {
            return [$from, $to, null];
        }

        $period = in_array($request->integer('period'), self::PERIODS, true) ? $request->integer('period') : 30;

        return [$today->copy()->subDays($period - 1), $today, $period];
    }

    /**
     * Headline metrics shown in the KPI row.
     *
     * @return list<array{label: string, icon: string, value: float|int, previous: float|int, format: string, hint: string, points?: bool, neutral?: bool}>
     */
    private function kpis(): array
    {
        return [
            ['label' => 'Active campaigns', 'icon' => 'megaphone', 'value' => 24, 'previous' => 20, 'format' => 'int', 'hint' => 'Campaigns live right now, including those in review.'],
            ['label' => 'Total reach', 'icon' => 'eye', 'value' => 12_800_000, 'previous' => 10_811_000, 'format' => 'compact', 'hint' => 'Unique accounts that saw campaign content, de-duplicated across creators.'],
            ['label' => 'Engagement rate', 'icon' => 'heart', 'value' => 4.87, 'previous' => 4.27, 'format' => 'percent', 'points' => true, 'hint' => 'Likes, comments, shares and saves divided by reach.'],
            ['label' => 'Campaign spend', 'icon' => 'wallet', 'value' => 82_650, 'previous' => 75_690, 'format' => 'money', 'neutral' => true, 'hint' => 'Creator fees, boosts and production paid in the period.'],
        ];
    }

    /**
     * Daily reach, engagement and conversions for the performance chart.
     *
     * @return array{labels: list<string>, series: array<string, list<int>>, totals: array<string, int>, previous: array<string, int>}
     */
    private function performance(Carbon $start, Carbon $end): array
    {
        $days = (int) $start->diffInDays($end) + 1;
        $random = new Randomizer(new Mt19937(1000 + $days));
        $labels = [];
        $series = ['reach' => [], 'engagement' => [], 'conversions' => []];

        for ($offset = $days - 1; $offset >= 0; $offset--) {
            $date = $end->copy()->subDays($offset);
            $weekendBoost = match ($date->dayOfWeek) {
                Carbon::FRIDAY => 1.2,
                Carbon::SATURDAY => 1.12,
                Carbon::THURSDAY => 1.05,
                default => 0.94,
            };
            $growth = 1 + 0.28 * (1 - min($date->diffInDays(Carbon::today()), 365) / 365);

            $reach = 46_000 * $weekendBoost * $growth * (0.9 + $random->getFloat(0, 0.2));
            $engagement = $reach * (0.044 + $random->getFloat(0, 0.008));

            $labels[] = $date->toDateString();
            $series['reach'][] = (int) round($reach);
            $series['engagement'][] = (int) round($engagement);
            $series['conversions'][] = (int) round($engagement * (0.026 + $random->getFloat(0, 0.008)));
        }

        $totals = array_map('array_sum', $series);

        return [
            'labels' => $labels,
            'series' => $series,
            'totals' => $totals,
            'previous' => [
                'reach' => (int) round($totals['reach'] * 0.845),
                'engagement' => (int) round($totals['engagement'] * 0.81),
                'conversions' => (int) round($totals['conversions'] * 0.79),
            ],
        ];
    }

    /**
     * Return on investment, unit costs and the monthly ROI trend.
     *
     * @return array{roi: int, previous: int, revenue: int, spend: int, conversions: int, engagements: int, monthly: list<array{month: string, roi: int}>}
     */
    private function roi(): array
    {
        $monthly = collect([286, 298, 341, 322, 305, 312])
            ->map(fn (int $roi, int $index): array => [
                'month' => Carbon::today()->startOfMonth()->subMonths(5 - $index)->format('M'),
                'roi' => $roi,
            ])
            ->all();

        return [
            'roi' => 312,
            'previous' => 325,
            'revenue' => 340_560,
            'spend' => 82_650,
            'conversions' => 18_420,
            'engagements' => 623_360,
            'monthly' => $monthly,
        ];
    }

    /**
     * Creator pipeline from first contact to published content.
     *
     * @return list<array{label: string, value: int}>
     */
    private function funnel(): array
    {
        return [
            ['label' => 'Influencers contacted', 'value' => 1_240],
            ['label' => 'Interested', 'value' => 684],
            ['label' => 'Contracted', 'value' => 312],
            ['label' => 'Content submitted', 'value' => 268],
            ['label' => 'Published', 'value' => 241],
        ];
    }

    /**
     * Latest creator conversations.
     *
     * @return list<array{name: string, tile: string, message: string, time: string, unread: int, online: bool, mine: bool}>
     */
    private function threads(): array
    {
        return [
            ['name' => 'Karim Haddad', 'tile' => 'amber', 'message' => 'Draft is ready, sending the cut tonight 🎬', 'time' => '5 min', 'unread' => 2, 'online' => true, 'mine' => false],
            ['name' => 'Yasmine Belkacem', 'tile' => 'coral', 'message' => 'Can we move the go-live to Thursday?', 'time' => '22 min', 'unread' => 1, 'online' => true, 'mine' => false],
            ['name' => 'Nour Hamidi', 'tile' => 'sky', 'message' => 'Do we resume the series next week?', 'time' => '1 h', 'unread' => 3, 'online' => false, 'mine' => false],
            ['name' => 'Lina Saïdi', 'tile' => 'pink', 'message' => 'Thanks for the feedback, updating captions now.', 'time' => '3 h', 'unread' => 0, 'online' => false, 'mine' => false],
            ['name' => 'Amine Tech', 'tile' => 'teal', 'message' => 'Paid this morning.', 'time' => 'Yesterday', 'unread' => 0, 'online' => false, 'mine' => true],
        ];
    }

    /**
     * Annual budget, pacing and monthly spend.
     *
     * @return array{total: int, spent: int, year_elapsed: float, monthly: list<array{month: string, amount: int}>}
     */
    private function budget(): array
    {
        $monthly = collect([7_260, 8_410, 9_120, 9_270, 10_010, 11_240])
            ->map(fn (int $amount, int $index): array => [
                'month' => Carbon::today()->startOfMonth()->subMonths(5 - $index)->format('M'),
                'amount' => $amount,
            ])
            ->all();

        return [
            'total' => 128_400,
            'spent' => 82_650,
            'year_elapsed' => Carbon::today()->dayOfYear / Carbon::today()->daysInYear,
            'monthly' => $monthly,
        ];
    }

    /**
     * The latest campaigns for the table.
     *
     * @return list<array{id: string, name: string, client: string, client_tile: string, objective: string, dates: string, influencers: list<array{name: string, tile: string}>, reach: ?int, engagement_rate: ?float, budget: int, spent: int, roi: ?int, status: string}>
     */
    private function recentCampaigns(): array
    {
        $creators = [
            'yasmine' => ['name' => 'Yasmine Belkacem', 'tile' => 'coral'],
            'karim' => ['name' => 'Karim Haddad', 'tile' => 'amber'],
            'nour' => ['name' => 'Nour Hamidi', 'tile' => 'sky'],
            'amine' => ['name' => 'Amine Tech', 'tile' => 'teal'],
            'lina' => ['name' => 'Lina Saïdi', 'tile' => 'pink'],
            'sofia' => ['name' => 'Sofia Merabet', 'tile' => 'green'],
            'meriem' => ['name' => 'Meriem Ould', 'tile' => 'magenta'],
            'rayan' => ['name' => 'Rayan Bouzid', 'tile' => 'violet'],
        ];

        $campaign = fn (string $id, string $name, string $client, string $clientTile, string $objective, string $dates, array $creatorKeys, ?int $reach, ?float $engagementRate, int $budget, int $spent, ?int $roi, string $status): array => [
            'id' => $id,
            'name' => $name,
            'client' => $client,
            'client_tile' => $clientTile,
            'objective' => $objective,
            'dates' => $dates,
            'influencers' => array_map(fn (string $key): array => $creators[$key], $creatorKeys),
            'reach' => $reach,
            'engagement_rate' => $engagementRate,
            'budget' => $budget,
            'spent' => $spent,
            'roi' => $roi,
            'status' => $status,
        ];

        return [
            $campaign('c1', 'Back to School Bundle', 'Numidia Stationery', 'sky', 'Conversions', 'Aug 20 – Oct 5', ['meriem', 'lina', 'sofia'], 1_820_000, 5.4, 12_000, 9_860, 286, 'Active'),
            $campaign('c2', '5G Launch Wave', 'Atlas Telecom', 'violet', 'Awareness', 'Sep 10 – Nov 30', ['amine', 'nour', 'karim'], 4_100_000, 3.9, 32_000, 14_200, 215, 'Active'),
            $campaign('c3', 'Mawlid Family Recipes', 'Sahel Foods', 'amber', 'Engagement', 'Aug 18 – Oct 2', ['karim', 'meriem'], 860_000, 7.2, 6_500, 2_940, 188, 'Active'),
            $campaign('c4', 'Autumn Coffee Rituals', 'Casbah Coffee Co.', 'amber', 'Awareness', 'Oct 12 – Nov 20', ['lina'], null, null, 7_500, 0, null, 'Draft'),
            $campaign('c5', 'Fall Fitness Challenge', 'Sahara Sport', 'green', 'Engagement', 'Sep 15 – Oct 31', ['sofia', 'rayan'], 640_000, 6.8, 9_000, 3_120, 164, 'Pending approval'),
            $campaign('c6', 'Ranked Night Series', 'Pixel Arena', 'sky', 'Engagement', 'Jul 3 – Sep 30', ['nour'], 1_200_000, 9.1, 8_000, 5_600, 231, 'Paused'),
            $campaign('c7', 'Summer Coast Collection', 'Medina Fashion', 'pink', 'Conversions', 'Jun 1 – Aug 31', ['lina', 'yasmine', 'sofia'], 2_900_000, 4.8, 18_500, 18_500, 402, 'Completed'),
            $campaign('c8', 'Fintech App Onboarding', 'PayDZ', 'magenta', 'Conversions', 'Sep 1 – Dec 15', ['amine', 'rayan'], 1_400_000, 3.6, 16_000, 7_800, 196, 'Active'),
            $campaign('c9', 'Skincare Science', 'Derma Atlas', 'teal', 'Conversions', 'Oct 20 – Dec 20', ['yasmine'], null, null, 11_000, 0, null, 'Draft'),
            $campaign('c10', 'Eid Gifts Guide', 'Souk Online', 'coral', 'Conversions', 'May 12 – Jun 8', ['yasmine', 'karim', 'lina'], 2_200_000, 5.1, 14_000, 13_720, 377, 'Completed'),
            $campaign('c11', 'Tlemcen Heritage Trail', 'Visit Tlemcen', 'green', 'Awareness', 'Apr 2 – May 10', ['rayan', 'meriem'], 540_000, 5.9, 5_000, 4_950, 142, 'Completed'),
            $campaign('c12', 'Ramadan Glow 2026', 'Kasbah Cosmetics', 'coral', 'Conversions', 'Feb 10 – Mar 22', ['yasmine', 'lina'], 3_200_000, 6.1, 24_000, 21_480, 348, 'Completed'),
        ];
    }

    /**
     * Campaign counts grouped by status.
     *
     * @return list<array{status: string, count: int}>
     */
    private function statusOverview(): array
    {
        return [
            ['status' => 'Active', 'count' => 24],
            ['status' => 'Pending approval', 'count' => 9],
            ['status' => 'Draft', 'count' => 18],
            ['status' => 'Paused', 'count' => 8],
            ['status' => 'Completed', 'count' => 89],
        ];
    }

    /**
     * Best performing creators this period.
     *
     * @return list<array{name: string, handle: string, tile: string, category: string, followers: int, engagement_rate: float, trend: float}>
     */
    private function topInfluencers(): array
    {
        return [
            ['name' => 'Karim Haddad', 'handle' => 'karimcooks', 'tile' => 'amber', 'category' => 'Food', 'followers' => 864_000, 'engagement_rate' => 8.4, 'trend' => 18.9],
            ['name' => 'Yasmine Belkacem', 'handle' => 'yasmine.bk', 'tile' => 'coral', 'category' => 'Beauty', 'followers' => 1_240_000, 'engagement_rate' => 5.8, 'trend' => 12.4],
            ['name' => 'Nour Hamidi', 'handle' => 'nourgaming', 'tile' => 'sky', 'category' => 'Gaming', 'followers' => 2_400_000, 'engagement_rate' => 9.3, 'trend' => 8.1],
            ['name' => 'Lina Saïdi', 'handle' => 'linastyle', 'tile' => 'pink', 'category' => 'Fashion', 'followers' => 542_000, 'engagement_rate' => 6.2, 'trend' => 6.6],
            ['name' => 'Rayan Bouzid', 'handle' => 'rayan.travels', 'tile' => 'violet', 'category' => 'Travel', 'followers' => 726_000, 'engagement_rate' => 5.2, 'trend' => -2.3],
        ];
    }

    /**
     * Recent workspace events for the activity timeline.
     *
     * @return list<array{icon: string, tone: string, actor: string, action: string, subject: string, time: string}>
     */
    private function activity(): array
    {
        return [
            ['icon' => 'megaphone', 'tone' => 'primary', 'actor' => 'You', 'action' => 'created', 'subject' => 'Autumn Coffee Rituals', 'time' => '12 min ago'],
            ['icon' => 'user-round-check', 'tone' => 'success', 'actor' => 'Karim Haddad', 'action' => 'accepted an invitation to', 'subject' => 'Mawlid Family Recipes', 'time' => '38 min ago'],
            ['icon' => 'file-check-2', 'tone' => 'info', 'actor' => 'Lina Saïdi', 'action' => 'submitted 3 posts for', 'subject' => 'Back to School Bundle', 'time' => '1 h ago'],
            ['icon' => 'credit-card', 'tone' => 'success', 'actor' => 'Finance', 'action' => 'paid $2,400 for', 'subject' => '5G Launch Wave', 'time' => '3 h ago'],
            ['icon' => 'circle-check-big', 'tone' => 'primary', 'actor' => 'Summer Coast Collection', 'action' => 'closed at 402% ROI', 'subject' => '', 'time' => 'Yesterday'],
        ];
    }
}
