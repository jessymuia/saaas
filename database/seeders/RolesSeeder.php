<?php

namespace Database\Seeders;

use App\Models\AppRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use App\Utils\AppPermissions;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        // Create the three tenant roles
        $roles = ['admin', 'accountant', 'caretaker'];
        foreach ($roles as $role) {
            AppRole::findOrCreate($role);
        }

        // Admin gets every permission
        $adminRole = AppRole::findOrCreate('admin');
        $adminRole->syncPermissions(Permission::all());

        // Accountant — finance-related PDFs
        $accountantRole = AppRole::findOrCreate('accountant');
        $accountantRole->givePermissionTo([
            AppPermissions::GENERATE_INVOICE_PDF,
            AppPermissions::GENERATE_MANUAL_INVOICE_PDF,
            AppPermissions::GENERATE_INVOICE_PAYMENT_PDF,
            AppPermissions::GENERATE_CLIENT_PDF,
        ]);

        // Caretaker — property & tenant PDFs
        $caretakerRole = AppRole::findOrCreate('caretaker');
        $caretakerRole->givePermissionTo([
            AppPermissions::GENERATE_PROPERTY_PDF,
            AppPermissions::GENERATE_TENANT_PDF,
        ]);

        $this->command->info('Roles seeded: admin, accountant, caretaker');
    }
}
