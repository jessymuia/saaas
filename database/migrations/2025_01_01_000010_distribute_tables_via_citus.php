<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (config('database.default') !== 'pgsql') return;

        DB::statement("CREATE EXTENSION IF NOT EXISTS citus");

        
        $constraints = DB::select("
            SELECT tc.table_name, tc.constraint_name
            FROM information_schema.table_constraints tc
            WHERE constraint_type = 'FOREIGN KEY'
            AND tc.table_schema = 'public'
        ");

        foreach ($constraints as $constraint) {
            DB::statement("
                ALTER TABLE \"{$constraint->table_name}\"
                DROP CONSTRAINT IF EXISTS \"{$constraint->constraint_name}\"
            ");
        }

        
        $referenceTables = [
            'ref_property_types',
            'ref_unit_types',
            'ref_payment_types',
            'ref_billing_types',
            'ref_tenancy_agreement_types',
            'ref_utilities',
            'services'
        ];

        foreach ($referenceTables as $table) {
            if (Schema::hasTable($table) && !$this->isDistributed($table)) {
                DB::statement("SELECT create_reference_table('$table')");
            }
        }

       
        $tablesToDistribute = [
            // Root Tables
            'users',
            'properties',
            'tenants',
            'clients',
            'property_owners',
            // Dependent Tables
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
            'audits'
        ];

        foreach ($tablesToDistribute as $table) {
            if (Schema::hasTable($table) && !$this->isDistributed($table)) {
                DB::statement("SELECT create_distributed_table('$table', 'saas_client_id')");
            }
        }
    }

    
    private function isDistributed(string $tableName): bool
    {
        $result = DB::selectOne("
            SELECT count(*) as count 
            FROM pg_dist_partition 
            WHERE logicalrelid = ?::regclass
        ", [$tableName]);

        return $result->count > 0;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void 
    {
        
    }
};