<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_payments', function (Blueprint $table) {
            $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: true, addAuditFk: true);

            $table->uuid('saas_client_id')->nullable()->index();
            $table->foreignUuid('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->foreignUuid('tenant_id')->nullable()->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('client_id')->nullable()->constrained('clients')->cascadeOnDelete();
            $table->foreignUuid('property_owner_id')->nullable()->constrained('property_owners')->cascadeOnDelete();
            $table->foreignUuid('payment_type_id')->constrained('ref_payment_types')->restrictOnDelete();
            $table->foreignUuid('received_by')->constrained('users')->restrictOnDelete();
            $table->dateTime('payment_date');
            $table->decimal('amount', 14, 2);
            $table->string('paid_by');
            $table->string('payment_reference')->nullable();
            $table->text('description')->nullable();
            $table->string('document_path')->nullable();
            $table->dateTime('document_generated_at')->nullable();
            $table->foreignUuid('document_generated_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->boolean('is_confirmed')->default(false);
            $table->dateTime('document_sent_at')->nullable();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_payments');
    }
};
