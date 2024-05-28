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
        Schema::create('invoice_payments', function (Blueprint $table) {
            $table = \App\Utils\AppUtils::defaultTableColumns($table);

            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('property_owner_id')->nullable();
            $table->unsignedBigInteger('payment_type_id');
            $table->unsignedBigInteger('received_by');
            $table->dateTime('payment_date');
            $table->decimal('amount', 14, 2);
            $table->string('paid_by');
            $table->string('payment_reference')->nullable();
            $table->text('description')->nullable();
            $table->string('document_path')->nullable();
            $table->dateTime('document_generated_at')->nullable();
            $table->unsignedBigInteger('document_generated_by')->nullable();
            $table->boolean('is_confirmed')->default(false);
            $table->dateTime('document_sent_at')->nullable();

            // foreign keys
            $table->foreign('payment_type_id')->references('id')->on('ref_payment_types');
            $table->foreign('received_by')->references('id')->on('users');
            $table->foreign('document_generated_by')->references('id')->on('users');
            $table->foreign('tenant_id')->references('id')->on('tenants');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('property_owner_id')->references('id')->on('property_owners');
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
