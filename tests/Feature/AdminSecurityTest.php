<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Invoice;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class AdminSecurityTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $this->seed(RoleSeeder::class);
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole('super_admin');

        return $user;
    }

    public function test_old_admin_path_not_accessible(): void
    {
        // /admin should not be accessible or should redirect/404 because path is now /d36-panel
        $this->get('/admin')->assertStatus(404);
        $this->get('/admin/login')->assertStatus(404);
    }

    public function test_new_admin_path_accessible(): void
    {
        $this->get('/d36-panel/login')->assertSuccessful();
    }

    public function test_login_rate_limiting(): void
    {
        $email = 'admin@example.com';
        $ip = '127.0.0.1';
        $throttleKey = 'admin-login|' . strtolower($email) . '|' . $ip;

        RateLimiter::clear($throttleKey);

        $component = Livewire::test(\App\Filament\Pages\Auth\Login::class);

        // Make 5 login attempts
        for ($i = 0; $i < 5; $i++) {
            $component->set('data.email', $email)
                ->set('data.password', 'wrong-password')
                ->call('authenticate');
        }

        // The 6th attempt should have the throttle error
        $component->set('data.email', $email)
            ->set('data.password', 'wrong-password')
            ->call('authenticate')
            ->assertHasErrors(['data.email'])
            ->assertSee('Terlalu banyak percobaan login');
    }

    public function test_invoice_pdf_is_not_publicly_accessible(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        $this->seed(RoleSeeder::class);
        $invoice = Invoice::create([
            'invoice_number' => 'INV-2026-0001',
            'bill_to_company' => 'PT X',
            'status' => 'draft',
            'file_path' => 'invoices/INV-2026-0001.pdf',
        ]);

        // Put fake file on local disk
        Storage::disk('local')->put('invoices/INV-2026-0001.pdf', 'dummy pdf content');

        // Verify public storage does not allow direct access (should be 403 or 404)
        $response = $this->get('/storage/invoices/INV-2026-0001.pdf');
        $this->assertTrue(in_array($response->status(), [403, 404]));

        // Accessing the download route without auth should redirect/abort
        $this->get(route('admin.invoices.download', ['invoice' => $invoice]))
            ->assertRedirect(); // redirects to login since guest
    }

    public function test_invoice_pdf_download_requires_permission(): void
    {
        Storage::fake('local');
        $this->seed(RoleSeeder::class);

        $invoice = Invoice::create([
            'invoice_number' => 'INV-2026-0002',
            'bill_to_company' => 'PT Y',
            'status' => 'draft',
            'file_path' => 'invoices/INV-2026-0002.pdf',
        ]);
        Storage::disk('local')->put('invoices/INV-2026-0002.pdf', 'dummy pdf content');

        // Create user without manage_invoices permission (e.g. admin_konten)
        $unauthorizedUser = User::factory()->create(['is_active' => true]);
        $unauthorizedUser->assignRole('admin_konten');

        $this->actingAs($unauthorizedUser, 'web')
            ->get(route('admin.invoices.download', ['invoice' => $invoice]))
            ->assertStatus(403);

        // Create super admin who has permission
        $admin = $this->admin();
        $this->actingAs($admin, 'web')
            ->get(route('admin.invoices.download', ['invoice' => $invoice]))
            ->assertSuccessful();
    }
}
