<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 13 — Row Level Security (RLS) for all distributed tables.
 *
 * Strategy:
 * 1. FORCE RLS on every distributed table — this applies even to the
 *    table owner (postgres superuser), closing the owner bypass loophole.
 * 2. Tenant isolation policy: rows visible only when saas_client_id
 *    matches the session variable `app.saas_client_id`.
 * 3. Super admin bypass policy: rows visible when the session variable
 *    `app.bypass_rls` is set to 'true' (set only in central context).
 * 4. current_setting(..., true) — the `true` makes it return NULL
 *    instead of throwing an error when the variable is not set.
 */
return new class extends Migration
{
    /**
     * All distributed tables that need RLS.
     * These all have saas_client_id as their distribution/partition key.
     */
    private array $distributedTables = [
        'properties',
        'units',
        'tenants',
        'tenancy_agreements',
        'tenancy_bills',
        'invoices',
        'invoice_payments',
        'manual_invoices',
        'manual_invoice_items',
        'credit_notes',
        'sent_emails',
        'email_attachments',
        'meter_readings',
        'unit_occupation_monthly_records',
        'tenancy_agreement_files',
        'property_management_users',
        'property_owners',
        'property_payment_details',
        'property_services',
        'property_utilities',
        'escalation_rates_and_amounts_logs',
        'audits',
        'users',
        'notifications',
        'otp_codes',
        'vacation_notices',
        'support_tickets',
        'usage_metrics',
        'saas_client_users',
        'jobs',
        'failed_jobs',
    ];

    public function up(): void
    {
        foreach ($this->distributedTables as $table) {
            // Check if table exists
            if (!Schema::hasTable($table)) {
                continue;
            }

            // Check if saas_client_id column exists
            if (!Schema::hasColumn($table, 'saas_client_id')) {
                continue;
            }

            // 1. Enable RLS on the table
            try {
                DB::statement("ALTER TABLE {$table} ENABLE ROW LEVEL SECURITY");
            } catch (\Exception $e) {
                // Already enabled, continue
            }

            // 2. FORCE RLS — applies even to the table owner
            try {
                DB::statement("ALTER TABLE {$table} FORCE ROW LEVEL SECURITY");
            } catch (\Exception $e) {
                // Already forced, continue
            }

            // 3. Tenant isolation policy
            try {
                DB::statement("
                    CREATE POLICY tenant_isolation_{$table}
                    ON {$table}
                    USING (
                        saas_client_id = current_setting('app.saas_client_id', true)::uuid
                    )
                ");
            } catch (\Exception $e) {
                // Policy already exists, continue
            }

            // 4. Super admin bypass policy
            try {
                DB::statement("
                    CREATE POLICY super_admin_bypass_{$table}
                    ON {$table}
                    USING (
                        current_setting('app.bypass_rls', true) = 'true'
                    )
                ");
            } catch (\Exception $e) {
                // Policy already exists, continue
            }
        }
    }

    public function down(): void
    {
        foreach ($this->distributedTables as $table) {
            // Check if table exists
            if (!Schema::hasTable($table)) {
                continue;
            }

            try {
                DB::statement("DROP POLICY IF EXISTS tenant_isolation_{$table} ON {$table}");
            } catch (\Exception $e) {
                // Continue
            }

            try {
                DB::statement("DROP POLICY IF EXISTS super_admin_bypass_{$table} ON {$table}");
            } catch (\Exception $e) {
                // Continue
            }

            try {
                DB::statement("ALTER TABLE {$table} NO FORCE ROW LEVEL SECURITY");
            } catch (\Exception $e) {
                // Continue
            }

            try {
                DB::statement("ALTER TABLE {$table} DISABLE ROW LEVEL SECURITY");
            } catch (\Exception $e) {
                // Continue
            }
        }
    }
};