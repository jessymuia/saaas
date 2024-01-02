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
        Schema::create('escalation_rates_and_amounts_logs', function (Blueprint $table) {
            $table = \App\Utils\AppUtils::defaultTableColumns($table);

            $table->unsignedBigInteger('tenancy_agreement_id');
            $table->decimal('escalation_rate', 5, 2);
            $table->decimal('previous_amount',14,2);
            $table->decimal('new_amount',14,2);
            $table->date('escalation_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('escalation_rates_and_amounts_logs');
    }
};
