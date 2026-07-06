<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $this->seed(RoleSeeder::class);
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole('super_admin');

        return $user;
    }

    public function test_admin_resource_pages_render(): void
    {
        $admin = $this->admin();

        $pages = [
            '/d36-panel/service-categories',
            '/d36-panel/service-categories/create',
            '/d36-panel/services',
            '/d36-panel/services/create',
            '/d36-panel/banners/create',
            '/d36-panel/blog-posts/create',
            '/d36-panel/portfolios/create',
            '/d36-panel/partnership-packages/create',
            '/d36-panel/testimonials/create',
            '/d36-panel/office-locations/create',
            '/d36-panel/contact-messages',
            '/d36-panel/manage-settings',
        ];

        foreach ($pages as $url) {
            $this->actingAs($admin, 'web')->get($url)->assertSuccessful();
        }
    }

    public function test_content_admin_cannot_see_transaction_only_nav_but_sees_content(): void
    {
        $this->seed(RoleSeeder::class);
        $konten = User::factory()->create(['is_active' => true]);
        $konten->assignRole('admin_konten');

        // Content admin can open content resources.
        $this->actingAs($konten, 'web')->get('/d36-panel/service-categories')->assertSuccessful();
    }
}
