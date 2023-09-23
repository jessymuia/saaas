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
        Schema::create('rent_payments', function (Blueprint $table) {
            $table = \App\Utils\AppUtils::defaultTableColumns($table);

            $table->unsignedBigInteger('tenancy_agreement_id');
            $table->unsignedBigInteger('payment_type_id');
            $table->unsignedBigInteger('received_by');
            $table->dateTime('payment_date');
            $table->decimal('amount', 14, 2);
            $table->string('paid_by');
            $table->string('payment_reference')->nullable();
            $table->text('description')->nullable();

            // foreign keys
            $table->foreign('tenancy_agreement_id')->references('id')->on('tenancy_agreements');
            $table->foreign('payment_type_id')->references('id')->on('ref_payment_types');
            $table->foreign('received_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rent_payments');
    }
};
