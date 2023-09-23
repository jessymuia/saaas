<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('meter_readings', function (Blueprint $table) {
            $table = \App\Utils\AppUtils::defaultTableColumns($table);

            $table->unsignedBigInteger('tenancy_agreement_id');
            $table->unsignedBigInteger('utility_id');
            $table->dateTime('reading_date');
            $table->decimal('current_reading', 16, 5);
            $table->decimal('previous_reading', 16, 5);
            $table->decimal('consumption', 16, 5);

            // foreign keys
            $table->foreign('tenancy_agreement_id')->references('id')->on('tenancy_agreements');
            $table->foreign('utility_id')->references('id')->on('ref_utilities');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meter_readings');
    }
};
