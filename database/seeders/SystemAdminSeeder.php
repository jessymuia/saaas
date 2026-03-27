<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\SaasClient;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SystemAdminSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create the Plan — use 'price_monthly' to match the migration column name
        $plan = Plan::updateOrCreate(
            ['slug' => 'system-plan'],
            [
                'name'          => 'System Plan',
                'price_monthly' => 0,  // fixed: was 'monthly_price'
                'price_yearly'  => 0,
            ]
        );

        // 2. Create the System Tenant
        $systemClient = SaasClient::updateOrCreate(
            ['slug' => 'system'],
            [
                'name'    => 'System',
                'domain'  => 'system.localhost',
                'status'  => 'active',
                'plan_id' => $plan->id,
            ]
        );

        // 3. Create the Admin User
        User::withoutGlobalScopes()->updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'           => 'Admin',
                'password'       => Hash::make('password'),
                'saas_client_id' => $systemClient->id,
                'phone_number'   => '000000000',
            ]
        );
    }
}