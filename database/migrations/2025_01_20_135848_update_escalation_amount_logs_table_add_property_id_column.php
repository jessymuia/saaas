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
        //
        Schema::table('escalation_rates_and_amounts_logs', function (Blueprint $table) {
            $table->foreignId('property_id')->nullable()->constrained();
            $table->foreign('tenancy_agreement_id')->references('id')->on('tenancy_agreements');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('escalation_rates_and_amounts_logs', function (Blueprint $table) {
            $table->dropForeign(['property_id']);
            $table->dropForeign(['tenancy_agreement_id']);
            $table->dropColumn('property_id');
            $table->dropColumn('tenancy_agreement_id');
        });
    }
};
