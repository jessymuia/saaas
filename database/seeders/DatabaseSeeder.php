<?php

namespace Database\Seeders;

use App\Jobs\CalculateUsageMetrics;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Core reference data ─────────────────────────────────────────────
        $this->call([
            PlansSeeder::class,
            RefDataSeeder::class,
        ]);

        // ── 2. Central platform admin ──────────────────────────────────────────
        $this->call([
            SystemAdminAccountSeeder::class,
            SystemAdminSeeder::class,
        ]);

        // ── 3. Spatie permissions & roles (shared across all tenants) ──────────
        $this->call([
            PermissionsSeeder::class,
            RolesSeeder::class,
        ]);

        // ── 4. Tenant SaaS clients (8 kakaye companies) ───────────────────────
        $this->call([
            SaasClientSeeder::class,
        ]);

        // ── 5. Users per tenant ────────────────────────────────────────────────
        $this->call([
            SaasClientUsersSeeder::class,
        ]);

        // ── 6. Recalculate usage metrics now that users exist ──────────────────
        $this->command->info('Calculating usage metrics for all clients...');
        CalculateUsageMetrics::dispatchSync();
        $this->command->info('Usage metrics calculated.');
    }
}
