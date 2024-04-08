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
        DB::statement('ALTER TABLE property_utilities ALTER COLUMN rate_per_unit SET DATA TYPE  DECIMAL(18,6)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        DB::statement('ALTER TABLE property_utilities ALTER COLUMN rate_per_unit SET DATA TYPE  DECIMAL(14,2)');
    }
};
