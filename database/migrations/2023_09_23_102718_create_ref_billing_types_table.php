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
        Schema::create('ref_billing_types', function (Blueprint $table) {
            $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: true, addAuditFk: false);

            $table->string('type');
            $table->string('description')->nullable();
            $table->integer('frequency_months');
            $table->integer('due_day');
        });
        // set constraint on due_day to be between 1 and 28
        DB::statement('ALTER TABLE ref_billing_types ADD CONSTRAINT chk_due_day CHECK (due_day BETWEEN 1 AND 28);');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_billing_types');
    }
};
