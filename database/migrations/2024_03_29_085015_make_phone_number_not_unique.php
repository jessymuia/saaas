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
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('phone_number',20)->change();
            // drop unique constraint
            $table->dropUnique('tenants_phone_number_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('phone_number',20)->unique()->change();
        });
    }
};
