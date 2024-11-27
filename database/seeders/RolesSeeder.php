<?php

namespace Database\Seeders;

use App\Models\AppRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use App\Utils\AppPermissions;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // create default roles
        $roles = [
            'admin',
            'accountant',
            'caretaker',
        ];

        foreach ($roles as $role) {
            \App\Models\AppRole::findOrCreate($role);
        }

        // define permissions for admin to be all
        $adminRole = AppRole::findOrCreate('admin');
        $allPermissions = Permission::all();
        $adminRole->syncPermissions($allPermissions);

        $accountantRole = AppRole::findOrCreate('accountant');
        $accountantRole->givePermissionTo([
            AppPermissions::GENERATE_INVOICE_PDF,
            AppPermissions::GENERATE_MANUAL_INVOICE_PDF,
            AppPermissions::GENERATE_INVOICE_PAYMENT_PDF,
            AppPermissions::GENERATE_CLIENT_PDF,
        ]);

        $caretakerRole = AppRole::findOrCreate('caretaker');
        $caretakerRole->givePermissionTo([
            AppPermissions::GENERATE_PROPERTY_PDF,
            AppPermissions::GENERATE_TENANT_PDF,
        ]);

        // assign the admin role to the initial user
        $user = \App\Models\User::query()
            ->where('email','lancerbrian001@gmail.com')
            ->first();

        $user->assignRole($adminRole);
    }
}
