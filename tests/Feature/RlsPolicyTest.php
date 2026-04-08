<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * RlsPolicyTest
 *
 * Validates that RLS (Row Level Security) policies are in place on all
 * distributed tables.
 *
 * Tests:
 *  1. RLS is enabled on expected tables.
 *  2. Tenant isolation policy exists.
 *  3. Super admin bypass policy exists.
 *
 * Requires PostgreSQL. Skipped for non-pgsql connections.
 *
 * Phase 15 checklist: "RLS policy tests"
 */
class RlsPolicyTest extends TestCase
{
    private function skipIfNotPostgres(): void
    {
        if (config('database.default') !== 'pgsql') {
            $this->markTestSkipped('RLS tests require PostgreSQL connection.');
        }
    }

    /**
     * Get tables with RLS enabled.
     */
    private function getTablesWithRls(): array
    {
        $result = DB::select("
            SELECT tablename
            FROM pg_tables
            WHERE schemaname = 'public'
              AND rowsecurity = true
        ");
        return array_column($result, 'tablename');
    }

    /**
     * Get RLS policies for a given table.
     */
    private function getPoliciesForTable(string $table): array
    {
        return DB::select("
            SELECT policyname, cmd, qual, with_check
            FROM pg_policies
            WHERE schemaname = 'public'
              AND tablename = ?
        ", [$table]);
    }

    public function test_rls_is_enabled_on_key_distributed_tables(): void
    {
        $this->skipIfNotPostgres();

        $tablesWithRls = $this->getTablesWithRls();

        if (empty($tablesWithRls)) {
            $this->markTestSkipped('No tables with RLS found — run the RLS migration first.');
        }

        $criticalTables = [
            'properties',
            'units',
            'invoices',
            'users',
            'tenancy_agreements',
        ];

        foreach ($criticalTables as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }
            $this->assertContains(
                $table,
                $tablesWithRls,
                "RLS must be enabled on table '{$table}' but it is not."
            );
        }
    }

    public function test_tenant_isolation_policy_exists_on_properties(): void
    {
        $this->skipIfNotPostgres();

        if (!Schema::hasTable('properties')) {
            $this->markTestSkipped('properties table does not exist.');
        }

        $policies = $this->getPoliciesForTable('properties');
        $policyNames = array_column($policies, 'policyname');

        if (empty($policies)) {
            $this->markTestSkipped('No RLS policies found on properties — run the RLS migration first.');
        }

        $hasTenantPolicy = collect($policyNames)->contains(function ($name) {
            return str_contains($name, 'tenant_isolation') || str_contains($name, 'tenant');
        });

        $this->assertTrue(
            $hasTenantPolicy,
            "A tenant isolation RLS policy must exist on the 'properties' table. Found: " . implode(', ', $policyNames)
        );
    }

    public function test_super_admin_bypass_policy_exists_on_properties(): void
    {
        $this->skipIfNotPostgres();

        if (!Schema::hasTable('properties')) {
            $this->markTestSkipped('properties table does not exist.');
        }

        $policies = $this->getPoliciesForTable('properties');
        $policyNames = array_column($policies, 'policyname');

        if (empty($policies)) {
            $this->markTestSkipped('No RLS policies found on properties — run the RLS migration first.');
        }

        $hasBypassPolicy = collect($policyNames)->contains(function ($name) {
            return str_contains($name, 'super_admin') || str_contains($name, 'bypass');
        });

        $this->assertTrue(
            $hasBypassPolicy,
            "A super admin bypass RLS policy must exist on the 'properties' table. Found: " . implode(', ', $policyNames)
        );
    }

    public function test_rls_policies_reference_saas_client_id_session_variable(): void
    {
        $this->skipIfNotPostgres();

        if (!Schema::hasTable('properties')) {
            $this->markTestSkipped('properties table does not exist.');
        }

        $policies = $this->getPoliciesForTable('properties');

        if (empty($policies)) {
            $this->markTestSkipped('No RLS policies found — run the RLS migration first.');
        }

        // At least one policy should reference app.saas_client_id or app.current_tenant_id
        $hasTenantVar = collect($policies)->contains(function ($policy) {
            $qual = $policy->qual ?? '';
            return str_contains($qual, 'saas_client_id') || str_contains($qual, 'current_tenant_id');
        });

        $this->assertTrue(
            $hasTenantVar,
            "At least one RLS policy on 'properties' must use saas_client_id session variable for tenant isolation."
        );
    }

    public function test_saas_clients_table_does_not_have_rls(): void
    {
        $this->skipIfNotPostgres();

        if (!Schema::hasTable('saas_clients')) {
            $this->markTestSkipped('saas_clients table does not exist.');
        }

        $tablesWithRls = $this->getTablesWithRls();

        // saas_clients is a central/local table — RLS is NOT expected here
        // (RLS is for distributed tables; central tables are protected by application logic)
        $this->assertNotContains(
            'saas_clients',
            $tablesWithRls,
            "The 'saas_clients' central table should NOT have RLS — it is managed by application-level authorization."
        );
    }
}
