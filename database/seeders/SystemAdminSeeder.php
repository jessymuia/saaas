<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\SaasClient;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SystemAdminSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create the Plan
        $plan = Plan::updateOrCreate(
            ['slug' => 'system-plan'],
            [
                'name'          => 'System Plan',
                'price_monthly' => 0,
                'price_yearly'  => 0,
            ]
        );

        // 2. Create the System Tenant
        // Use DB::table to bypass Stancl Tenancy's model events entirely
        // This prevents the TenantCreated event from trying to create a tenant database
        $existing = DB::table('saas_clients')->where('slug', 'system')->first();

        if (! $existing) {
            DB::table('saas_clients')->insert([
                'id'           => (string) \Illuminate\Support\Str::uuid(),
                'name'         => 'System',
                'slug'         => 'system',
                'email'        => 'system@localhost',
                'contact_name' => 'System Admin',
                'phone'        => '000000000',
                'status'       => 'active',
                'plan_id'      => $plan->id,
                'is_suspended' => false,
                'data'         => '{}',
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        $systemClient = DB::table('saas_clients')->where('slug', 'system')->first();

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