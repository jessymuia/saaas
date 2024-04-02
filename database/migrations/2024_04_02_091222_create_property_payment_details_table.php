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
        Schema::create('property_payment_details', function (Blueprint $table) {
            $table = \App\Utils\AppUtils::defaultTableColumns($table);

            $table->unsignedBigInteger('property_id')->unique();
            $table->string('account_name')->nullable(false);
            $table->string('account_number')->nullable(false);
            $table->string('bank_name')->nullable(false);
            $table->string('mpesa_paybill_number')->nullable(false);

            // foreign keys
            $table->foreign('property_id')->references('id')->on('properties');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_payment_details');
    }
};
