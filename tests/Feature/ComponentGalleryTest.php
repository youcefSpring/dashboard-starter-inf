<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComponentGalleryTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_view_the_component_gallery(): void
    {
        $this->get(route('components'))->assertRedirect(route('login'));
    }

    public function test_component_gallery_renders_every_section(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('components'))
            ->assertOk()
            ->assertSeeInOrder(['Buttons', 'Badges &amp; chips', 'Avatars', 'Forms', 'Tabs &amp; menus', 'Overlays', 'Feedback', 'Data display', 'Charts', 'Domain cards', 'Appearance'], false);
    }
}
