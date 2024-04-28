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
        Schema::create('manual_invoice_items', function (Blueprint $table) {
            $table = \App\Utils\AppUtils::defaultTableColumns($table);

            $table->unsignedBigInteger('manual_invoice_id');
            $table->string('name',500);
            $table->date('bill_date');
            $table->date('due_date');
            $table->integer('quantity')->default(1); // added quantity column (default 1
            $table->decimal('amount', 14, 2);
            $table->decimal('vat', 14, 2)->default(0.0);
            $table->decimal('total_amount',14,2);
            $table->unsignedBigInteger('billing_type_id');
            $table->enum('category', ['ordinary', 'balance_carried_forward'])->default('ordinary');

            // foreign keys
            $table->foreign('billing_type_id')->references('id')->on('ref_billing_types');
            $table->foreign('manual_invoice_id')->references('id')->on('manual_invoices');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manual_invoice_items');
    }
};
