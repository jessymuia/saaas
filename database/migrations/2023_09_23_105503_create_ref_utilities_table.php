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
        Schema::create('ref_utilities', function (Blueprint $table) {
            $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: true, addAuditFk: true);

            $table->string('name');
            $table->string('description')->nullable();
            $table->string('unit_of_measurement');

            // add unique constraints
            $table->unique(['name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_utilities');
    }
};
