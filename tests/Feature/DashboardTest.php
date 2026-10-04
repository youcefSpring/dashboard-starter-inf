<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_redirects_to_the_dashboard(): void
    {
        $this->get('/')->assertRedirect('/dashboard');
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_signed_in_users_see_the_dashboard_greeting(): void
    {
        $user = User::factory()->create(['name' => 'Sarah Benali']);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Sarah')
            ->assertSee(__('Campaign performance'));
    }

    public function test_chart_period_follows_the_query_string(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard', ['period' => 7]))
            ->assertOk()
            ->assertViewHas('period', 7)
            ->assertViewHas('performance', fn (array $performance): bool => count($performance['labels']) === 7);
    }

    public function test_unsupported_chart_periods_fall_back_to_thirty_days(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('dashboard', ['period' => 365]))
            ->assertViewHas('period', 30);
    }

    public function test_a_custom_date_range_sets_the_chart_window(): void
    {
        $from = now()->subDays(13)->toDateString();
        $to = now()->toDateString();

        $this->actingAs(User::factory()->create())
            ->get(route('dashboard', ['from' => $from, 'to' => $to]))
            ->assertOk()
            ->assertViewHas('period', null)
            ->assertViewHas('from', $from)
            ->assertViewHas('performance', fn (array $performance): bool => count($performance['labels']) === 14
                && $performance['labels'][0] === $from);
    }

    public function test_invalid_custom_ranges_fall_back_to_the_default_period(): void
    {
        $user = User::factory()->create();

        foreach ([
            ['from' => now()->toDateString(), 'to' => now()->subDays(5)->toDateString()],
            ['from' => now()->toDateString(), 'to' => now()->addDays(5)->toDateString()],
            ['from' => 'not-a-date', 'to' => now()->toDateString()],
        ] as $range) {
            $this->actingAs($user)
                ->get(route('dashboard', $range))
                ->assertOk()
                ->assertViewHas('period', 30);
        }
    }
}
