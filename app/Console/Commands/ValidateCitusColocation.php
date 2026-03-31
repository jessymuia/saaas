<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * ValidateCitusColocation
 *
 * Validates that all expected distributed tables:
 *  1. Are actually distributed in Citus (present in pg_dist_partition).
 *  2. Share the same colocationid (i.e., co-located on saas_client_id).
 *
 * Usage:
 *   php artisan citus:validate-colocation
 *
 * Phase 5 checklist item: "Co-location verified after all tables created"
 */
class ValidateCitusColocation extends Command
{
    protected $signature = 'citus:validate-colocation
                            {--fail-fast : Stop on first error}
                            {--json : Output results as JSON}';

    protected $description = 'Validate all distributed tables are present in Citus and share the same colocation ID';

    /**
     * Tables that must be distributed on saas_client_id.
     * These should match the list in 2025_01_01_000010_distribute_tables_via_citus.php
     */
    private array $expectedDistributedTables = [
        'users',
        'properties',
        'tenants',
        'clients',
        'property_owners',
        'units',
        'tenancy_agreements',
        'invoices',
        'tenancy_bills',
        'invoice_payments',
        'credit_notes',
        'meter_readings',
        'property_utilities',
        'property_services',
        'property_payment_details',
        'vacation_notices',
        'tenancy_agreement_files',
        'unit_occupation_monthly_records',
        'sent_emails',
        'email_attachments',
        'escalation_rates_and_amounts_logs',
        'audits',
        'manual_invoices',
        'manual_invoice_items',
        'support_tickets',
        'usage_metrics',
        'notifications',
        'jobs',
        'failed_jobs',
    ];

    /**
     * Tables that must be reference tables (replicated, not distributed).
     */
    private array $expectedReferenceTables = [
        'ref_property_types',
        'ref_unit_types',
        'ref_payment_types',
        'ref_billing_types',
        'ref_tenancy_agreement_types',
        'ref_utilities',
        'services',
    ];

    public function handle(): int
    {
        $this->info('==> Citus Co-location Validation');
        $this->newLine();

        if (config('database.default') !== 'pgsql') {
            $this->warn('Not using PostgreSQL — skipping Citus validation.');
            return self::SUCCESS;
        }

        // Check Citus extension is installed
        try {
            $citusVersion = DB::selectOne("SELECT citus_version() AS version");
            $this->line("    Citus version: <info>{$citusVersion->version}</info>");
        } catch (\Throwable $e) {
            $this->error('Citus extension is not installed or not enabled: ' . $e->getMessage());
            return self::FAILURE;
        }

        $errors = [];
        $results = [];

        // ─── Validate distributed tables ─────────────────────────────────────
        $this->info('--- Checking distributed tables ---');

        $distributed = DB::select("
            SELECT logicalrelid::text AS table_name, colocationid
            FROM pg_dist_partition
            WHERE partmethod = 'h'
            ORDER BY table_name
        ");

        $distributedMap = collect($distributed)->keyBy('table_name');
        $colocationIds = collect($distributed)->pluck('colocationid')->unique();

        foreach ($this->expectedDistributedTables as $table) {
            if (!Schema::hasTable($table)) {
                $results[$table] = ['status' => 'SKIP', 'reason' => 'table does not exist'];
                $this->line("  <comment>SKIP</comment>  {$table} (table does not exist)");
                continue;
            }

            if (!$distributedMap->has($table)) {
                $errors[] = "Table '{$table}' is NOT distributed in Citus.";
                $results[$table] = ['status' => 'FAIL', 'reason' => 'not distributed'];
                $this->line("  <error>FAIL</error>  {$table} — NOT distributed");

                if ($this->option('fail-fast')) {
                    break;
                }
                continue;
            }

            $colocationId = $distributedMap->get($table)->colocationid;
            $results[$table] = ['status' => 'OK', 'colocation_id' => $colocationId];
            $this->line("  <info>OK</info>    {$table} (colocationid={$colocationId})");
        }

        // ─── Check all tables share same colocationid ─────────────────────────
        $this->newLine();
        $this->info('--- Checking co-location consistency ---');

        if ($colocationIds->count() > 1) {
            $errors[] = 'Distributed tables have MULTIPLE colocation IDs: ' . $colocationIds->implode(', ') . '. All tenant tables must share the same colocationid!';
            $this->error('FAIL: Multiple colocation IDs found: ' . $colocationIds->implode(', '));
        } elseif ($colocationIds->count() === 1) {
            $this->line('  <info>OK</info>    All distributed tables share colocationid=' . $colocationIds->first());
        }

        // ─── Validate reference tables ────────────────────────────────────────
        $this->newLine();
        $this->info('--- Checking reference tables ---');

        $referenceTables = DB::select("
            SELECT logicalrelid::text AS table_name
            FROM pg_dist_partition
            WHERE partmethod = 'n'
        ");
        $referenceMap = collect($referenceTables)->pluck('table_name')->flip();

        foreach ($this->expectedReferenceTables as $table) {
            if (!Schema::hasTable($table)) {
                $this->line("  <comment>SKIP</comment>  {$table} (table does not exist)");
                continue;
            }

            if (!$referenceMap->has($table)) {
                $errors[] = "Table '{$table}' is NOT a reference table in Citus.";
                $this->line("  <error>FAIL</error>  {$table} — NOT a reference table");
            } else {
                $this->line("  <info>OK</info>    {$table} (reference table ✓)");
            }
        }

        // ─── Output raw colocation table ──────────────────────────────────────
        $this->newLine();
        $this->info('--- Full pg_dist_partition summary ---');
        $this->table(
            ['Table', 'Colocation ID', 'Method'],
            collect($distributed)->map(fn($r) => [
                $r->table_name,
                $r->colocationid,
                'distributed (hash)',
            ])->toArray()
        );

        // ─── JSON output ──────────────────────────────────────────────────────
        if ($this->option('json')) {
            $this->newLine();
            $this->line(json_encode([
                'errors'   => $errors,
                'results'  => $results,
                'colocation_ids' => $colocationIds->values()->toArray(),
            ], JSON_PRETTY_PRINT));
        }

        // ─── Final result ─────────────────────────────────────────────────────
        $this->newLine();
        if (!empty($errors)) {
            $this->error('==> VALIDATION FAILED — ' . count($errors) . ' error(s):');
            foreach ($errors as $e) {
                $this->error("    • {$e}");
            }
            return self::FAILURE;
        }

        $this->info('==> All validations PASSED ✓');
        return self::SUCCESS;
    }
}
