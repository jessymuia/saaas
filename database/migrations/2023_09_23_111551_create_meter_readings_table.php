<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meter_readings', function (Blueprint $table) {
            $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: true, addAuditFk: true);

            $table->uuid('saas_client_id')->nullable()->index();
            $table->foreignUuid('unit_id')->constrained('units')->cascadeOnDelete();
            $table->foreignUuid('utility_id')->constrained('ref_utilities')->restrictOnDelete();
            $table->dateTime('reading_date');
            $table->decimal('current_reading', 16, 5);
            $table->decimal('previous_reading', 16, 5);
            $table->decimal('consumption', 16, 5);
            $table->tinyInteger('has_bill')->default(0);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meter_readings');
    }
};
