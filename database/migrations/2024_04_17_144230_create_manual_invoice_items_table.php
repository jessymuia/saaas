<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manual_invoice_items', function (Blueprint $table) {
            $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: true, addAuditFk: false);

            $table->uuid('saas_client_id')->nullable()->index();
            $table->foreignUuid('manual_invoice_id')->constrained('manual_invoices')->cascadeOnDelete();
            $table->string('name', 500);
            $table->date('bill_date');
            $table->date('due_date');
            $table->integer('quantity')->default(1);
            $table->decimal('amount', 14, 2);
            $table->decimal('vat', 14, 2)->default(0.0);
            $table->decimal('total_amount', 14, 2);
            $table->foreignUuid('billing_type_id')->constrained('ref_billing_types')->restrictOnDelete();
            $table->enum('category', ['ordinary', 'balance_carried_forward'])->default('ordinary');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manual_invoice_items');
    }
};
