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
            $table->foreignId('property_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('escalation_rates_and_amounts_logs', function (Blueprint $table){
            // make property id required
            $table->foreignId('property_id')->nullable()->change();
        });
    }
};
