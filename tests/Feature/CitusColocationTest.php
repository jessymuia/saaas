<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * CitusColocationTest
 *
 * Validates that:
 *  1. All expected distributed tables are present in pg_dist_partition.
 *  2. All distributed tables share the same colocationid.
 *  3. All expected reference tables are present in pg_dist_partition.
 *
 * These tests require a real PostgreSQL + Citus connection.
 * They are skipped automatically when not using pgsql or when Citus is not installed.
 *
 * Phase 15 checklist: "tests include validating citus co-location"
 */
class CitusColocationTest extends TestCase
{
    private function skipIfNotCitus(): void
    {
        if (config('database.default') !== 'pgsql') {
            $this->markTestSkipped('Citus tests require PostgreSQL connection.');
        }

        try {
            DB::selectOne("SELECT citus_version()");
        } catch (\Throwable) {
            $this->markTestSkipped('Citus extension is not installed — skipping co-location tests.');
        }
    }

    private function getDistributedTables(): array
    {
        return DB::select("
            SELECT logicalrelid::text AS table_name, colocationid
            FROM pg_dist_partition
            WHERE partmethod = 'h'
        ");
    }

    private function getReferenceTables(): array
    {
        return DB::select("
            SELECT logicalrelid::text AS table_name
            FROM pg_dist_partition
            WHERE partmethod = 'n'
        ");
    }

    public function test_citus_extension_is_installed(): void
    {
        $this->skipIfNotCitus();

        $result = DB::selectOne("SELECT citus_version() AS version");
        $this->assertNotEmpty($result->version, 'Citus version should be non-empty');
    }

    public function test_key_tables_are_distributed(): void
    {
        $this->skipIfNotCitus();

        $distributed = collect($this->getDistributedTables())->pluck('table_name')->toArray();

        $requiredTables = [
            'properties',
            'units',
            'tenants',
            'users',
            'invoices',
            'tenancy_agreements',
            'tenancy_bills',
        ];

        foreach ($requiredTables as $table) {
            if (!Schema::hasTable($table)) {
                continue; // Skip if table doesn't exist (migration not run)
            }
            $this->assertContains(
                $table,
                $distributed,
                "Table '{$table}' must be distributed in Citus but is not in pg_dist_partition."
            );
        }
    }

    public function test_all_distributed_tables_share_same_colocation_id(): void
    {
        $this->skipIfNotCitus();

        $distributed = $this->getDistributedTables();

        if (empty($distributed)) {
            $this->markTestSkipped('No distributed tables found — run migrations first.');
        }

        $colocationIds = collect($distributed)->pluck('colocationid')->unique();

        $this->assertCount(
            1,
            $colocationIds,
            'All distributed tables must share the same colocationid for proper tenant co-location. '
            . 'Found multiple colocationids: ' . $colocationIds->implode(', ')
        );
    }

    public function test_reference_tables_are_registered(): void
    {
        $this->skipIfNotCitus();

        $referenceTables = collect($this->getReferenceTables())->pluck('table_name')->toArray();

        $expectedRefTables = [
            'ref_property_types',
            'ref_unit_types',
            'ref_payment_types',
            'ref_billing_types',
            'ref_tenancy_agreement_types',
        ];

        foreach ($expectedRefTables as $table) {
            if (!Schema::hasTable($table)) {
                continue; // Skip if table doesn't exist
            }
            $this->assertContains(
                $table,
                $referenceTables,
                "Table '{$table}' must be a Citus reference table but is not in pg_dist_partition."
            );
        }
    }

    public function test_tenant_tables_are_not_in_pg_dist_for_central_tables(): void
    {
        $this->skipIfNotCitus();

        $distributed = collect($this->getDistributedTables())->pluck('table_name')->toArray();

        // Central/local tables MUST NOT be distributed
        $centralTables = ['saas_clients', 'plans', 'subscriptions', 'subscription_payments', 'system_admins', 'domains'];

        foreach ($centralTables as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }
            $this->assertNotContains(
                $table,
                $distributed,
                "Central table '{$table}' must NOT be distributed — it should be a local/central table."
            );
        }
    }
}
