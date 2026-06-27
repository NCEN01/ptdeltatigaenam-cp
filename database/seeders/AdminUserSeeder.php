<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@deltatigaenam.com');
        $password = env('ADMIN_PASSWORD');

        if (blank($password)) {
            $this->command->warn('ADMIN_PASSWORD kosong di .env — melewati AdminUserSeeder.');

            return;
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => env('ADMIN_NAME', 'Super Admin'),
                'password' => Hash::make($password),
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );

        $user->syncRoles(['super_admin']);

        $this->command->info("Super admin siap: {$email}");
    }
}
