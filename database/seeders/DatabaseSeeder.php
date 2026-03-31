<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Plan — use updateOrCreate via DB to avoid duplicate slug error
        $planId = DB::table('plans')->where('slug', 'trial-plan')->value('id');

        if (! $planId) {
            $planId = DB::table('plans')->insertGetId([
                'name'           => 'Trial Plan',
                'slug'           => 'trial-plan',
                'description'    => 'Default trial plan',
                'price_monthly'  => 0.00,
                'price_yearly'   => 0.00,
                'max_properties' => 10,
                'max_units'      => 100,
                'max_users'      => 5,
                'is_active'      => true,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }

        // 2. SaaS Client — skip if already exists
        $clientExists = DB::table('saas_clients')->where('slug', 'test-client')->exists();

        if (! $clientExists) {
            DB::table('saas_clients')->insert([
                'id'           => (string) Str::uuid(),
                'name'         => 'Test Client',
                'slug'         => 'test-client',
                'data'         => '{}',
                'email'        => 'test@example.com',
                'contact_name' => 'Test Contact',
                'phone'        => '+254712345678',
                'status'       => 'trial',
                'plan_id'      => $planId,
                'is_suspended' => false,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        // 3. Sequential Seeders
        $this->call([
            Systemadminaccountseeder::class,
            SystemAdminSeeder::class,
            PermissionsSeeder::class,
            UserSeeder::class,
            RolesSeeder::class,
        ]);
    }
}