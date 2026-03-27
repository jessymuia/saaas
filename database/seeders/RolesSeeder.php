<?php

namespace Database\Seeders;

use App\Models\AppRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use App\Utils\AppPermissions;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['admin', 'accountant', 'caretaker'];

        foreach ($roles as $role) {
            \App\Models\AppRole::findOrCreate($role);
        }

        $adminRole = AppRole::findOrCreate('admin');
        $adminRole->syncPermissions(Permission::all());

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

        // Fetch the real UUID for the saas_client seeded earlier
        $saasClientId = DB::table('saas_clients')->where('slug', 'test-client')->value('id');

        // Assign the admin role to the initial user
        $user = \App\Models\User::where('email', 'lancerbrian001@gmail.com')
            ->where('saas_client_id', $saasClientId)
            ->first();

        if ($user) {
            $user->assignRole($adminRole);
        } else {
            $this->command->warn("User lancerbrian001@gmail.com not found. Skipping role assignment.");
        }
    }
}