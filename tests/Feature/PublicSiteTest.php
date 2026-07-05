<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\PartnershipRegistration;
use Database\Seeders\OfficeLocationSeeder;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([SettingSeeder::class, OfficeLocationSeeder::class]);
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
        $response = $this->post('/id/kontak', [
            'name' => 'Budi',
            'email' => 'budi@example.com',
            'phone' => '08123456789',
            'subject' => 'Pertanyaan',
            'message' => 'Saya tertarik dengan pelatihan.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('contact_messages', [
            'email' => 'budi@example.com',
            'locale' => 'id',
            'is_read' => false,
        ]);
    }

    public function test_contact_form_validates(): void
    {
        $this->post('/id/kontak', ['name' => ''])
            ->assertSessionHasErrors(['name', 'email', 'message']);
        $this->assertSame(0, ContactMessage::count());
    }

    public function test_partnership_form_stores_lead(): void
    {
        $response = $this->post('/id/kemitraan/daftar', [
            'company_name' => 'PT Maju',
            'company_address' => 'Jakarta',
            'pic_name' => 'Sari',
            'phone' => '0812000',
            'email' => 'sari@maju.co',
            'notes' => 'In-house training',
        ]);

        $response->assertRedirect();
        $reg = PartnershipRegistration::first();
        $this->assertNotNull($reg);
        $this->assertEquals('baru', $reg->status);
        $this->assertStringStartsWith('REG-', $reg->registration_number);
    }
}
