<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\Customer;
use App\Models\PartnershipRegistration;
use App\Models\User;
use Database\Seeders\OfficeSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['captcha.enabled' => false]);
        $this->seed([SettingSeeder::class, OfficeSeeder::class]);
    }

    public function test_root_redirects_to_default_locale(): void
    {
        $this->get('/')->assertRedirect('/id');
    }

    public function test_home_renders_in_both_locales(): void
    {
        $this->get('/id')->assertOk()->assertSee('human capital', false);
        $this->get('/en')->assertOk()->assertSee('sustainable human capital transformation', false);
    }

    public function test_hreflang_tags_present(): void
    {
        $this->get('/id')
            ->assertSee('hreflang="id"', false)
            ->assertSee('hreflang="en"', false)
            ->assertSee('hreflang="x-default"', false);
    }

    public function test_contact_form_stores_message(): void
    {
        $this->seed(RoleSeeder::class);
        $superAdmin = User::factory()->create(['is_active' => true]);
        $superAdmin->assignRole('super_admin');
        $adminKonten = User::factory()->create(['is_active' => true]);
        $adminKonten->assignRole('admin_konten');
        $adminTransaksi = User::factory()->create(['is_active' => true]);
        $adminTransaksi->assignRole('admin_transaksi');

        $response = $this->post('/id/kontak', [
            'name' => 'Budi',
            'email' => 'budi@example.com',
            'phone' => '08123456789',
            'subject' => 'Pertanyaan Pelatihan',
            'message' => 'Saya tertarik dengan pelatihan.',
        ]);

        $response->assertRedirect();
        $message = ContactMessage::where('email', 'budi@example.com')->first();
        $this->assertNotNull($message);
        $this->assertFalse($message->is_read);

        // Database notification sent to admin_konten and super_admin with direct link, not admin_transaksi
        $this->assertCount(1, $superAdmin->notifications);
        $this->assertCount(1, $adminKonten->notifications);
        $this->assertCount(0, $adminTransaksi->notifications);

        $notifData = $adminKonten->notifications->first()->data;
        $this->assertSame('Pesan Kontak Baru', $notifData['title'] ?? null);
        $this->assertNotEmpty($notifData['actions'] ?? []);
        $this->assertStringContainsString('/d36-panel/contact-messages/'.$message->id, $notifData['actions'][0]['url'] ?? '');
    }

    public function test_contact_form_validates(): void
    {
        $this->post('/id/kontak', ['name' => ''])
            ->assertSessionHasErrors(['name', 'email', 'message']);
        $this->assertSame(0, ContactMessage::count());
    }

    public function test_contact_form_rejects_too_short_message(): void
    {
        $this->post('/id/kontak', [
            'name' => 'Budi',
            'email' => 'budi@example.com',
            'message' => 'halo',
        ])->assertSessionHasErrors(['message']);

        $this->assertSame(0, ContactMessage::count());
    }

    private function customer(): Customer
    {
        return Customer::create([
            'name' => 'Sari',
            'email' => 'sari@maju.co',
            'password' => 'password',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);
    }

    public function test_partnership_form_stores_lead(): void
    {
        $response = $this->actingAs($this->customer(), 'customer')->post('/id/kemitraan/daftar', [
            'company_name' => 'PT Maju',
            'company_address' => 'Jakarta',
            'pic_name' => 'Sari',
            'phone' => '0812000',
            'email' => 'sari@maju.co',
            'notes' => 'In-house training',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $reg = PartnershipRegistration::first();
        $this->assertNotNull($reg);
        $this->assertEquals('baru', $reg->status);
        $this->assertStringStartsWith('REG-', $reg->registration_number);
    }

    public function test_partnership_rejects_past_or_duplicate_meeting_times(): void
    {
        $same = now()->addDays(3)->format('Y-m-d\TH:i');

        $this->actingAs($this->customer(), 'customer')->post('/id/kemitraan/daftar', [
            'company_name' => 'PT Maju',
            'company_address' => 'Jakarta',
            'pic_name' => 'Sari',
            'phone' => '0812000',
            'email' => 'sari@maju.co',
            'preferred_meeting_at' => now()->subDay()->format('Y-m-d\TH:i'),
            'alternative_meeting_at' => now()->subDay()->format('Y-m-d\TH:i'),
        ])->assertSessionHasErrors(['preferred_meeting_at', 'alternative_meeting_at']);

        $this->post('/id/kemitraan/daftar', [
            'company_name' => 'PT Maju',
            'company_address' => 'Jakarta',
            'pic_name' => 'Sari',
            'phone' => '0812000',
            'email' => 'sari@maju.co',
            'preferred_meeting_at' => $same,
            'alternative_meeting_at' => $same,
        ])->assertSessionHasErrors(['alternative_meeting_at']);

        $this->assertSame(0, PartnershipRegistration::count());
    }
}
