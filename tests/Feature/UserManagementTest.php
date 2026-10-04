<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_be_filtered_by_status(): void
    {
        $admin = User::factory()->create(['name' => 'Admin Person']);
        User::factory()->create(['name' => 'Verified Person']);
        User::factory()->unverified()->create(['name' => 'Pending Person']);

        $this->actingAs($admin)
            ->get(route('users.index', ['status' => 'unverified']))
            ->assertOk()
            ->assertSee('Pending Person')
            ->assertDontSee('Verified Person');
    }

    public function test_users_can_be_sorted_by_an_allowed_column(): void
    {
        $admin = User::factory()->create(['name' => 'Mia']);
        User::factory()->create(['name' => 'Zoe']);
        User::factory()->create(['name' => 'Adam']);

        $this->actingAs($admin)
            ->get(route('users.index', ['sort' => 'name', 'direction' => 'asc']))
            ->assertOk()
            ->assertSeeInOrder(['Adam', 'Mia', 'Zoe']);
    }

    public function test_unknown_sort_columns_and_page_sizes_are_ignored(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('users.index', ['sort' => 'password', 'per_page' => 1000]))
            ->assertOk()
            ->assertViewHas('filters', fn (array $filters): bool => $filters['sort'] === 'created_at')
            ->assertViewHas('perPage', 10);
    }

    public function test_a_user_can_be_added(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('users.store'), [
                'name' => 'New Person',
                'email' => 'new@example.com',
                'password' => 'password',
            ])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('status');

        $user = User::firstWhere('email', 'new@example.com');
        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('password', $user->password));
    }

    public function test_adding_a_user_reports_errors_in_its_own_bag(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $this->actingAs(User::factory()->create())
            ->post(route('users.store'), ['name' => 'Someone', 'email' => 'taken@example.com', 'password' => 'password'])
            ->assertSessionHasErrorsIn('createUser', 'email');
    }

    public function test_a_user_can_be_updated(): void
    {
        $target = User::factory()->create();

        $this->actingAs(User::factory()->create())
            ->patch(route('users.update', $target), ['name' => 'Renamed', 'email' => 'renamed@example.com'])
            ->assertSessionHasNoErrors();

        $target->refresh();
        $this->assertSame('Renamed', $target->name);
        $this->assertNull($target->email_verified_at);
    }

    public function test_updating_a_user_reports_errors_in_its_own_bag(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);
        $target = User::factory()->create();

        $this->actingAs(User::factory()->create())
            ->patch(route('users.update', $target), ['name' => 'Renamed', 'email' => 'taken@example.com'])
            ->assertSessionHasErrorsIn('updateUser', 'email');
    }

    public function test_a_user_can_be_deleted(): void
    {
        $target = User::factory()->create();

        $this->actingAs(User::factory()->create())
            ->delete(route('users.destroy', $target))
            ->assertSessionHas('status');

        $this->assertModelMissing($target);
    }

    public function test_users_cannot_delete_themselves_from_the_table(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->delete(route('users.destroy', $user))
            ->assertForbidden();

        $this->assertModelExists($user);
    }

    public function test_bulk_delete_removes_selected_users_but_never_the_current_one(): void
    {
        $user = User::factory()->create();
        [$first, $second, $kept] = User::factory(3)->create();

        $this->actingAs($user)
            ->delete(route('users.bulk-destroy'), ['ids' => [$first->id, $second->id, $user->id]])
            ->assertSessionHas('status', '2 users were deleted.');

        $this->assertModelMissing($first);
        $this->assertModelMissing($second);
        $this->assertModelExists($kept);
        $this->assertModelExists($user);
    }

    public function test_bulk_delete_requires_a_selection(): void
    {
        $this->actingAs(User::factory()->create())
            ->delete(route('users.bulk-destroy'))
            ->assertSessionHasErrors('ids');
    }

    public function test_selected_users_can_be_exported_as_csv(): void
    {
        $admin = User::factory()->create();
        $picked = User::factory()->create(['email' => 'picked@example.com']);
        User::factory()->create(['email' => 'skipped@example.com']);

        $response = $this->actingAs($admin)->get(route('users.export', ['ids' => [$picked->id]]));

        $response->assertOk()->assertDownload('users-'.now()->format('Y-m-d').'.csv');
        $csv = $response->streamedContent();
        $this->assertStringContainsString('picked@example.com', $csv);
        $this->assertStringNotContainsString('skipped@example.com', $csv);
    }

    public function test_export_follows_the_current_filters(): void
    {
        $admin = User::factory()->create(['email' => 'admin@example.com']);
        User::factory()->unverified()->create(['email' => 'pending@example.com']);

        $csv = $this->actingAs($admin)->get(route('users.export', ['status' => 'unverified']))->streamedContent();

        $this->assertStringContainsString('pending@example.com', $csv);
        $this->assertStringNotContainsString('admin@example.com', $csv);
    }

    public function test_guests_cannot_manage_users(): void
    {
        $target = User::factory()->create();

        $this->post(route('users.store'))->assertRedirect(route('login'));
        $this->patch(route('users.update', $target))->assertRedirect(route('login'));
        $this->delete(route('users.destroy', $target))->assertRedirect(route('login'));
        $this->get(route('users.export'))->assertRedirect(route('login'));
    }
}
