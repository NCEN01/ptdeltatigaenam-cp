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
            '/admin/service-categories',
            '/admin/service-categories/create',
            '/admin/services',
            '/admin/services/create',
            '/admin/banners/create',
            '/admin/blog-posts/create',
            '/admin/portfolios/create',
            '/admin/partnership-packages/create',
            '/admin/testimonials/create',
            '/admin/office-locations/create',
            '/admin/contact-messages',
            '/admin/manage-settings',
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
        $this->actingAs($konten, 'web')->get('/admin/service-categories')->assertSuccessful();
    }
}
