<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('unit_occupation_monthly_records', function (Blueprint $table) {
            $table = \App\Utils\AppUtils::defaultTableColumns($table);

            $table->unsignedBigInteger('unit_id');
            $table->unsignedBigInteger('tenancy_agreement_id');
            $table->date('from_date');
            $table->date('end_date');
            $table->unsignedBigInteger('tenancy_bill_id');

            // foreign keys
            $table->foreign('unit_id')->references('id')->on('units');
            $table->foreign('tenancy_agreement_id')->references('id')->on('tenancy_agreements');
            $table->foreign('tenancy_bill_id')->references('id')->on('tenancy_bills');
        });

        // constraint to ensure that the from_date is not greater than the end_date
        DB::statement('ALTER TABLE unit_occupation_monthly_records ADD CONSTRAINT chk_from_date_less_than_end_date CHECK (from_date <= end_date);');

        // execute this statement SET GLOBAL sql_require_primary_key = ON;
//        DB::statement('SET SESSION sql_require_primary_key = ON;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_occupation_monthly_records');
    }
};
