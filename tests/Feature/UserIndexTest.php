<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_list_users(): void
    {
        $this->get(route('users.index'))->assertRedirect(route('login'));
    }

    public function test_users_are_listed(): void
    {
        $user = User::factory()->create(['name' => 'Sarah Benali']);
        User::factory()->create(['name' => 'Karim Haddad']);

        $this->actingAs($user)
            ->get(route('users.index'))
            ->assertOk()
            ->assertSee('Sarah Benali')
            ->assertSee('Karim Haddad');
    }

    public function test_users_can_be_searched_by_name_or_email(): void
    {
        $admin = User::factory()->create(['name' => 'Admin Person']);
        User::factory()->create(['name' => 'Sarah Benali', 'email' => 'sarah@example.com']);
        User::factory()->create(['name' => 'Karim Haddad', 'email' => 'karim@cooks.test']);

        $this->actingAs($admin)
            ->get(route('users.index', ['q' => 'cooks.test']))
            ->assertOk()
            ->assertSee('Karim Haddad')
            ->assertDontSee('Sarah Benali');
    }

    public function test_an_empty_state_is_shown_when_nothing_matches(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('users.index', ['q' => 'no-such-person']))
            ->assertOk()
            ->assertSee(__('No users found'));
    }
}
