<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unit_occupation_monthly_records', function (Blueprint $table) {
            
            $table->unsignedBigInteger('id')->autoIncrement();

            $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: false, addAuditFk: false);

           
            $table->uuid('saas_client_id');

            $table->unsignedBigInteger('unit_id');
            $table->unsignedBigInteger('tenancy_agreement_id');
            $table->date('from_date');
            $table->date('end_date');
            $table->unsignedBigInteger('tenancy_bill_id');

           
            $table->primary(['id', 'saas_client_id']);

            
            $table->foreign(['unit_id', 'saas_client_id'])
                  ->references(['id', 'saas_client_id'])
                  ->on('units')
                  ->onDelete('cascade');

            $table->foreign(['tenancy_agreement_id', 'saas_client_id'])
                  ->references(['id', 'saas_client_id'])
                  ->on('tenancy_agreements')
                  ->onDelete('cascade');

            $table->foreign(['tenancy_bill_id', 'saas_client_id'])
                  ->references(['id', 'saas_client_id'])
                  ->on('tenancy_bills')
                  ->onDelete('cascade');

            
            $table->foreign(['created_by', 'saas_client_id'])
                  ->references(['id', 'saas_client_id'])
                  ->on('users')
                  ->cascadeOnDelete();

            $table->foreign(['updated_by', 'saas_client_id'])
                  ->references(['id', 'saas_client_id'])
                  ->on('users')
                  ->cascadeOnDelete();

            $table->foreign(['deleted_by', 'saas_client_id'])
                  ->references(['id', 'saas_client_id'])
                  ->on('users')
                  ->cascadeOnDelete();
        });

        
        DB::statement('ALTER TABLE unit_occupation_monthly_records ADD CONSTRAINT chk_from_date_less_than_end_date CHECK (from_date <= end_date);');
    }

    public function down(): void
    {
        Schema::dropIfExists('unit_occupation_monthly_records');
    }
};
