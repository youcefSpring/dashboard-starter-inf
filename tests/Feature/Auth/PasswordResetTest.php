<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_page_renders(): void
    {
        $this->get(route('password.request'))
            ->assertOk()
            ->assertSee(__('Reset your password'));
    }

    public function test_reset_link_is_emailed_to_existing_users(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post(route('password.email'), ['email' => $user->email])
            ->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_unknown_emails_get_the_same_response_without_a_notification(): void
    {
        Notification::fake();

        $this->post(route('password.email'), ['email' => 'nobody@example.com'])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('status');

        Notification::assertNothingSent();
    }

    public function test_reset_password_page_renders_with_the_token(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post(route('password.email'), ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user): bool {
            $this->get(route('password.reset', ['token' => $notification->token, 'email' => $user->email]))
                ->assertOk()
                ->assertSee($notification->token)
                ->assertSee($user->email);

            return true;
        });
    }

    public function test_password_can_be_reset_with_a_valid_token(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post(route('password.email'), ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user): bool {
            $this->post(route('password.store'), [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
                ->assertSessionHasNoErrors()
                ->assertRedirect(route('login'));

            $this->assertTrue(Hash::check('new-password', $user->fresh()->password));

            return true;
        });
    }

    public function test_password_cannot_be_reset_with_an_invalid_token(): void
    {
        $user = User::factory()->create();

        $this->post(route('password.store'), [
            'token' => 'invalid-token',
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])->assertSessionHasErrors('email');

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }
}
