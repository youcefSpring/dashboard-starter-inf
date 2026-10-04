<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_renders(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee(__('Welcome back'));
    }

    public function test_users_can_sign_in_with_valid_credentials(): void
    {
        $user = User::factory()->create();

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_are_sent_to_the_page_they_originally_requested(): void
    {
        $user = User::factory()->create();

        $this->get(route('settings.edit'))->assertRedirect(route('login'));

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('settings.edit'));
    }

    public function test_users_cannot_sign_in_with_a_wrong_password(): void
    {
        $user = User::factory()->create();

        $this->from(route('login'))
            ->post(route('login'), [
                'email' => $user->email,
                'password' => 'wrong-password',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email' => __('auth.failed')]);

        $this->assertGuest();
    }

    public function test_login_is_throttled_after_five_failed_attempts(): void
    {
        $user = User::factory()->create();

        foreach (range(1, 5) as $attempt) {
            $this->post(route('login'), ['email' => $user->email, 'password' => 'wrong-password']);
        }

        $this->post(route('login'), ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
        $this->assertTrue(RateLimiter::tooManyAttempts(strtolower($user->email).'|127.0.0.1', 5));
    }

    public function test_signed_in_users_are_redirected_away_from_the_login_page(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('login'))
            ->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_sign_out(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
