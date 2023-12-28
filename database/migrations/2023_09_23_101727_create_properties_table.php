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
        Schema::create('properties', function (Blueprint $table) {
            $table = \App\Utils\AppUtils::defaultTableColumns($table);

            $table->string('name');
            $table->string('address');
            $table->integer('number_of_units')->nullable();
            $table->unsignedBigInteger('property_type_id')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_vatable')->default(false);

            // foreign keys
            $table->foreign('property_type_id')->references('id')->on('ref_property_types');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
