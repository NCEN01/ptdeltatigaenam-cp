<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_register_and_lands_on_verification_notice(): void
    {
        $response = $this->post('/id/daftar', [
            'name' => 'Rina',
            'email' => 'rina@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/id/verifikasi-email');
        $this->assertDatabaseHas('customers', ['email' => 'rina@example.com', 'is_active' => true]);
        $this->assertAuthenticatedAs(Customer::first(), 'customer');
    }

    public function test_unverified_customer_is_blocked_from_account(): void
    {
        $customer = Customer::create([
            'name' => 'Rina', 'email' => 'r@e.com', 'password' => Hash::make('password123'),
            'preferred_locale' => 'id', 'is_active' => true,
        ]);

        $this->actingAs($customer, 'customer')->get('/id/akun')->assertRedirect('/id/verifikasi-email');
    }

    public function test_verified_customer_can_view_profile_and_orders(): void
    {
        $customer = Customer::create([
            'name' => 'Rina', 'email' => 'r@e.com', 'password' => Hash::make('password123'),
            'preferred_locale' => 'id', 'is_active' => true,
        ]);
        $customer->markEmailAsVerified();

        $this->actingAs($customer, 'customer')->get('/id/akun')->assertOk()->assertSee('Rina', false);
        $this->actingAs($customer, 'customer')->get('/id/akun/pesanan')->assertOk();
    }

    public function test_guest_cannot_access_account(): void
    {
        $this->get('/id/akun')->assertRedirect('/id/masuk');
    }

    public function test_login_and_logout(): void
    {
        $customer = Customer::create([
            'name' => 'Rina', 'email' => 'r@e.com', 'password' => Hash::make('password123'),
            'preferred_locale' => 'id', 'is_active' => true, 'email_verified_at' => now(),
        ]);

        $this->post('/id/masuk', ['email' => 'r@e.com', 'password' => 'password123'])
            ->assertRedirect();
        $this->assertAuthenticatedAs($customer, 'customer');

        $this->post('/id/keluar')->assertRedirect('/id');
        $this->assertGuest('customer');
    }

    public function test_customer_cannot_access_admin_panel(): void
    {
        $this->seed(RoleSeeder::class);
        $customer = Customer::create([
            'name' => 'Rina', 'email' => 'r@e.com', 'password' => Hash::make('password123'),
            'preferred_locale' => 'id', 'is_active' => true, 'email_verified_at' => now(),
        ]);

        // A customer is not a panel User; /d36-panel must not authenticate them.
        $this->actingAs($customer, 'customer')->get('/d36-panel')->assertRedirect();
    }
}
