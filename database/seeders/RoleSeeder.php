<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Permissions grouped by responsibility (PRD §8.9 matrix).
     */
    public const CONTENT_PERMISSIONS = [
        'manage_services',
        'manage_banners',
        'manage_blog',
        'manage_agenda',
        'manage_portfolio',
        'manage_partners',
        'manage_partnership_packages',
        'manage_settings',
        'manage_offices',
        'manage_missions',
        'view_contact_messages',
    ];

    public const TRANSACTION_PERMISSIONS = [
        'view_orders',
        'view_transactions',
        'manage_partnership_registrations',
        'manage_invoices',
    ];

    public const ADMIN_PERMISSIONS = [
        'manage_users',
    ];

    public function run(): void
    {
        $all = array_merge(
            self::CONTENT_PERMISSIONS,
            self::TRANSACTION_PERMISSIONS,
            self::ADMIN_PERMISSIONS,
        );

        foreach ($all as $name) {
            Permission::findOrCreate($name, 'web');
        }

        $superAdmin = Role::findOrCreate('super_admin', 'web');
        $adminTransaksi = Role::findOrCreate('admin_transaksi', 'web');
        $adminKonten = Role::findOrCreate('admin_konten', 'web');

        $superAdmin->syncPermissions($all);
        $adminKonten->syncPermissions(self::CONTENT_PERMISSIONS);
        $adminTransaksi->syncPermissions(self::TRANSACTION_PERMISSIONS);
    }
}
