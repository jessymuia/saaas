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
        Schema::create('units', function (Blueprint $table) {
            $table = \App\Utils\AppUtils::defaultTableColumns($table);

            $table->string('name');
            $table->unsignedBigInteger('property_id');
            $table->unsignedBigInteger('unit_type_id');

            // foreign keys
            $table->foreign('property_id')->references('id')->on('properties');
            $table->foreign('unit_type_id')->references('id')->on('ref_unit_types');

            // constraint to ensure uniqueness of name and property_id
            $table->unique(['name', 'property_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
