<?php

namespace Tests\Feature;

use App\Jobs\BroadcastServicePromoJob;
use App\Models\Agenda;
use App\Models\Customer;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Notifications\NewServicePromoNotification;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
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

    public function test_agenda_within_one_week_auto_drafts_to_service_and_notifies_admins(): void
    {
        $this->seed(RoleSeeder::class);

        $superAdmin = User::factory()->create(['is_active' => true]);
        $superAdmin->assignRole('super_admin');
        $adminKonten = User::factory()->create(['is_active' => true]);
        $adminKonten->assignRole('admin_konten');
        $adminTransaksi = User::factory()->create(['is_active' => true]);
        $adminTransaksi->assignRole('admin_transaksi');

        ServiceCategory::create([
            'name' => ['id' => 'Pelatihan', 'en' => 'Training'],
            'slug' => 'pelatihan',
            'is_active' => true,
        ]);

        // Agenda > 7 days away should NOT auto-draft on save
        Agenda::create([
            'title' => ['id' => 'Agenda Bulan Depan', 'en' => 'Next Month Agenda'],
            'slug' => 'agenda-bulan-depan',
            'status' => 'published',
            'starts_at' => now()->addDays(14),
            'ends_at' => now()->addDays(15),
            'location' => 'Jakarta',
        ]);
        $this->assertNull(Service::where('slug', 'agenda-bulan-depan')->first());

        // Agenda <= 7 days away auto-drafts into Service (is_active = false) + ServiceSchedule + notifies review
        $soonAgenda = Agenda::create([
            'title' => ['id' => 'Bootcamp K3 Minggu Ini', 'en' => 'Safety Bootcamp This Week'],
            'slug' => 'bootcamp-k3-minggu-ini',
            'excerpt' => ['id' => 'Ringkasan bootcamp', 'en' => 'Bootcamp summary'],
            'content' => ['id' => '<p>Isi lengkap</p>', 'en' => '<p>Full content</p>'],
            'status' => 'published',
            'starts_at' => now()->addDays(4)->setTime(9, 0),
            'ends_at' => now()->addDays(5)->setTime(16, 0),
            'location' => 'Bandung',
        ]);

        $draftService = Service::where('slug', 'bootcamp-k3-minggu-ini')->first();
        $this->assertNotNull($draftService);
        $this->assertFalse($draftService->is_active);
        $this->assertSame('2 hari', $draftService->getTranslation('duration', 'id'));
        $this->assertCount(1, $draftService->schedules);

        // Notification sent to admin_konten and super_admin (not admin_transaksi)
        $this->assertCount(1, $superAdmin->notifications);
        $this->assertCount(1, $adminKonten->notifications);
        $this->assertCount(0, $adminTransaksi->notifications);

        $notifData = $adminKonten->notifications->first()->data;
        $this->assertSame('Draft Layanan dari Agenda Siap Direview', $notifData['title'] ?? null);
        $this->assertStringContainsString('/d36-panel/services/'.$draftService->id.'/edit', $notifData['actions'][0]['url'] ?? '');

        // Idempotent: calling syncUpcomingAgendasToDraftServices again does not duplicate
        $this->assertSame(0, Agenda::syncUpcomingAgendasToDraftServices());
        $this->assertNull($soonAgenda->draftToService());
    }

    public function test_broadcast_service_promo_job_queues_emails_for_active_verified_customers(): void
    {
        Notification::fake();

        $category = ServiceCategory::create([
            'name' => ['id' => 'Pelatihan', 'en' => 'Training'],
            'slug' => 'pelatihan',
            'is_active' => true,
        ]);

        $service = Service::create([
            'service_category_id' => $category->id,
            'title' => ['id' => 'Pelatihan Leadership Eksekutif', 'en' => 'Executive Leadership Training'],
            'slug' => 'pelatihan-leadership-eksekutif',
            'short_description' => ['id' => 'Program intensif 2 hari', 'en' => '2-day intensive programme'],
            'price' => 2500000,
            'discount_active' => true,
            'discount_original_price' => 3500000,
            'is_purchasable' => true,
            'is_active' => true,
        ]);

        $verifiedCustomer = Customer::create([
            'name' => 'Rina',
            'email' => 'rina@example.com',
            'password' => 'password',
            'email_verified_at' => now(),
            'preferred_locale' => 'id',
            'is_active' => true,
        ]);

        $unverifiedCustomer = Customer::create([
            'name' => 'Tono',
            'email' => 'tono@example.com',
            'password' => 'password',
            'email_verified_at' => null,
            'is_active' => true,
        ]);

        $inactiveCustomer = Customer::create([
            'name' => 'Dedi',
            'email' => 'dedi@example.com',
            'password' => 'password',
            'email_verified_at' => now(),
            'is_active' => false,
        ]);

        (new BroadcastServicePromoJob($service->id, 'Diskon khusus pendaftaran minggu ini!'))->handle();

        Notification::assertSentTo($verifiedCustomer, NewServicePromoNotification::class, function (NewServicePromoNotification $n) use ($verifiedCustomer) {
            $mail = $n->toMail($verifiedCustomer);

            return str_contains($mail->subject, 'Promo Hemat')
                && str_contains(implode(' ', $mail->introLines), 'Diskon khusus pendaftaran minggu ini!');
        });
        Notification::assertNotSentTo($unverifiedCustomer, NewServicePromoNotification::class);
        Notification::assertNotSentTo($inactiveCustomer, NewServicePromoNotification::class);
    }
}
