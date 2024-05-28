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
        Schema::table('invoice_payments', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('tenant_id')->nullable()->change();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('property_owner_id')->nullable();

            // drop foreign key reference
            $table->dropForeign('invoice_payments_invoice_id_foreign');

            // add foreign key references
            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('property_owner_id')->references('id')->on('property_owners');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_payments', function (Blueprint $table) {
            //
            // drop foreign keys
            $table->dropConstrainedForeignId('client_id');
            $table->dropConstrainedForeignId('property_owner_id');

            // revert previous foreign key
            $table->foreign('invoice_id')->references('id')->on('invoices');

            // revert tenant id column to previous state
            $table->unsignedBigInteger('tenant_id')->nullable(false)->change();
        });
    }
};
