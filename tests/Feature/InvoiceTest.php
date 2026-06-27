<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\User;
use App\Services\InvoiceService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_service_calculates_totals_and_generates_pdf(): void
    {
        Storage::fake('public');

        $invoice = Invoice::create([
            'bill_to_company' => 'PT Contoh',
            'tax' => 50000,
            'status' => 'draft',
            'issued_date' => now(),
        ]);

        $invoice->items()->createMany([
            ['description' => 'Pelatihan A', 'quantity' => 2, 'unit_price' => 1000000, 'amount' => 0],
            ['description' => 'Pelatihan B', 'quantity' => 1, 'unit_price' => 500000, 'amount' => 0],
        ]);

        $service = new InvoiceService();
        $service->recalculate($invoice->fresh('items'));

        $invoice->refresh();
        $this->assertEquals(2500000, (float) $invoice->subtotal);
        $this->assertEquals(2550000, (float) $invoice->total);
        $this->assertEquals(2000000, (float) InvoiceItem::where('description', 'Pelatihan A')->first()->amount);

        $path = $service->generatePdf($invoice);
        Storage::disk('public')->assertExists($path);
        $this->assertStringEndsWith('.pdf', $path);
        $this->assertEquals($path, $invoice->fresh()->file_path);
    }

    public function test_invoice_number_auto_generated(): void
    {
        $invoice = Invoice::create(['bill_to_company' => 'PT X', 'status' => 'draft']);
        $this->assertStringStartsWith('INV-', $invoice->invoice_number);
    }

    public function test_content_admin_cannot_access_orders(): void
    {
        $this->seed(RoleSeeder::class);
        $konten = User::factory()->create(['is_active' => true]);
        $konten->assignRole('admin_konten');

        $this->actingAs($konten, 'web')->get('/admin/orders')->assertForbidden();
    }

    public function test_transaction_admin_can_access_orders(): void
    {
        $this->seed(RoleSeeder::class);
        $txn = User::factory()->create(['is_active' => true]);
        $txn->assignRole('admin_transaksi');

        $this->actingAs($txn, 'web')->get('/admin/orders')->assertSuccessful();
    }
}
