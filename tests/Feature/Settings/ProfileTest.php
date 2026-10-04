<?php

namespace Tests\Feature\Settings;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_settings_page_renders(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('settings.edit'))
            ->assertOk()
            ->assertSee(__('Delete account'));
    }

    public function test_profile_can_be_updated(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->patch(route('settings.profile.update'), [
                'name' => 'New Name',
                'email' => 'new@example.com',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('settings.edit'));

        $user->refresh();

        $this->assertSame('New Name', $user->name);
        $this->assertSame('new@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_verification_is_kept_when_the_email_is_unchanged(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->patch(route('settings.profile.update'), [
                'name' => 'New Name',
                'email' => $user->email,
            ])
            ->assertSessionHasNoErrors();

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_email_must_be_unique_to_another_user(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->patch(route('settings.profile.update'), [
                'name' => $user->name,
                'email' => 'taken@example.com',
            ])
            ->assertSessionHasErrors('email');
    }

    public function test_users_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->delete(route('settings.profile.destroy'), ['password' => 'password'])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('login'));

        $this->assertGuest();
        $this->assertModelMissing($user);
    }

    public function test_correct_password_is_required_to_delete_the_account(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('settings.edit'))
            ->delete(route('settings.profile.destroy'), ['password' => 'wrong-password'])
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect(route('settings.edit'));

        $this->assertModelExists($user);
    }
}
