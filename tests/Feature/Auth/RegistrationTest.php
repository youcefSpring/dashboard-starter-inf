<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_page_renders(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertSee(__('Create your account'));
    }

    public function test_new_users_can_register_and_are_signed_in(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Sarah Benali',
            'email' => 'sarah@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::firstWhere('email', 'sarah@example.com');

        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('password', $user->password));
        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard'))
            ->assertSessionHas('status', 'Welcome aboard, Sarah!');
    }

    public function test_registration_requires_a_unique_email(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $this->post(route('register'), [
            'name' => 'Someone',
            'email' => 'taken@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_registration_requires_a_confirmed_password(): void
    {
        $this->post(route('register'), [
            'name' => 'Someone',
            'email' => 'someone@example.com',
            'password' => 'password',
            'password_confirmation' => 'different',
        ])->assertSessionHasErrors('password');

        $this->assertDatabaseMissing('users', ['email' => 'someone@example.com']);
    }
}
