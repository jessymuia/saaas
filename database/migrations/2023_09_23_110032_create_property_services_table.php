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
        Schema::create('property_services', function (Blueprint $table) {
            $table = \App\Utils\AppUtils::defaultTableColumns($table);

            $table->unsignedBigInteger('property_id');
            $table->unsignedBigInteger('service_id');
            $table->decimal('rate', 14, 2);
            $table->unsignedBigInteger('billing_type_id');

            // foreign keys
            $table->foreign('property_id')->references('id')->on('properties');
            $table->foreign('service_id')->references('id')->on('services');
            $table->foreign('billing_type_id')->references('id')->on('ref_billing_types');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_services');
    }
};
