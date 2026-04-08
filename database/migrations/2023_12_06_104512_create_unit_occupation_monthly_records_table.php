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
            $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: true, addAuditFk: true);

            $table->uuid('saas_client_id')->nullable()->index();
            $table->foreignUuid('unit_id')->constrained('units')->cascadeOnDelete();
            $table->foreignUuid('tenancy_agreement_id')->constrained('tenancy_agreements')->cascadeOnDelete();
            $table->date('from_date');
            $table->date('end_date');
            $table->foreignUuid('tenancy_bill_id')->constrained('tenancy_bills')->cascadeOnDelete();

        });

        DB::statement('ALTER TABLE unit_occupation_monthly_records ADD CONSTRAINT chk_from_date_less_than_end_date CHECK (from_date <= end_date);');
    }

    public function down(): void
    {
        Schema::dropIfExists('unit_occupation_monthly_records');
    }
};
