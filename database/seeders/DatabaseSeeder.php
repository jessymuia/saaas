<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Plan — uses auto-increment bigint id, insertGetId() works fine here
        $planId = DB::table('plans')->insertGetId([
            'name'           => 'Trial Plan',
            'slug'           => 'trial-plan',
            'price_monthly'  => 0.00,
            'price_yearly'   => 0.00,
            'is_active'      => true,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        // 2. SaaS Client — uuid('id') requires a UUID string, not an integer
        DB::table('saas_clients')->insert([
            'id'         => (string) Str::uuid(),
            'name'       => 'Test Client',
            'slug'       => 'test-client',
            'domain'     => 'test.localhost',
            'status'     => 'trial',
            'plan_id'    => $planId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Sequential Seeders
        $this->call([
            Systemadminaccountseeder::class,
            SystemAdminSeeder::class,
            PermissionsSeeder::class,
            UserSeeder::class,   // Must exist before RolesSeeder
            RolesSeeder::class,  // Assigns role to user
        ]);
    }
}