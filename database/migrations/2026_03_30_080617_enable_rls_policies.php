<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

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
 *
 * How to set the session variable in Laravel (TenancyServiceProvider or middleware):
 *   DB::statement("SET app.saas_client_id = '{$tenant->id}'");
 *
 * How to set the super admin bypass (central panel middleware):
 *   DB::statement("SET app.bypass_rls = 'true'");
 *   DB::statement("RESET app.bypass_rls"); // clear after use
 *
 * Super admin bypass command to apply manually on coordinator:
 *   ALTER ROLE app_user BYPASSRLS;  -- grants DB-level bypass to app user
 * OR use the session-variable policy approach below (preferred — more granular).
 *
 * NOTE: Run this migration ONLY on the Citus coordinator.
 * The coordinator propagates DDL to workers automatically for distributed tables.
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
            // 1. Enable RLS on the table
            DB::statement("ALTER TABLE {$table} ENABLE ROW LEVEL SECURITY");

            // 2. FORCE RLS — applies even to the table owner (closes owner bypass loophole)
            DB::statement("ALTER TABLE {$table} FORCE ROW LEVEL SECURITY");

            // 3. Tenant isolation policy
            //    current_setting('app.saas_client_id', true) returns NULL (not an error)
            //    when the variable is not set, so central context queries return nothing
            //    unless the bypass policy (below) is also active.
            DB::statement("
                CREATE POLICY tenant_isolation_{$table}
                ON {$table}
                USING (
                    saas_client_id = current_setting('app.saas_client_id', true)::uuid
                )
            ");

            // 4. Super admin bypass policy
            //    When app.bypass_rls = 'true' is set in the session,
            //    all rows are visible regardless of saas_client_id.
            //    This is set ONLY in the central/super admin context.
            DB::statement("
                CREATE POLICY super_admin_bypass_{$table}
                ON {$table}
                USING (
                    current_setting('app.bypass_rls', true) = 'true'
                )
            ");
        }
    }

    public function down(): void
    {
        foreach ($this->distributedTables as $table) {
            DB::statement("DROP POLICY IF EXISTS tenant_isolation_{$table} ON {$table}");
            DB::statement("DROP POLICY IF EXISTS super_admin_bypass_{$table} ON {$table}");
            DB::statement("ALTER TABLE {$table} NO FORCE ROW LEVEL SECURITY");
            DB::statement("ALTER TABLE {$table} DISABLE ROW LEVEL SECURITY");
        }
    }
};