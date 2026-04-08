<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenancy_bills', function (Blueprint $table) {
            $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: true, addAuditFk: true);

            $table->uuid('saas_client_id')->nullable()->index();
            $table->foreignUuid('tenancy_agreement_id')->constrained('tenancy_agreements')->cascadeOnDelete();
            $table->string('name', 500);
            $table->date('bill_date');
            $table->date('due_date');
            $table->decimal('amount', 14, 2);
            $table->decimal('vat', 14, 2)->default(0.0);
            $table->decimal('total_amount', 14, 2);
            $table->foreignUuid('billing_type_id')->constrained('ref_billing_types')->restrictOnDelete();
            $table->foreignUuid('service_id')->nullable()->constrained('services')->cascadeOnDelete();
            $table->foreignUuid('utility_id')->nullable()->constrained('ref_utilities')->cascadeOnDelete();
            $table->boolean('is_deposit')->default(false);
            $table->foreignUuid('invoice_id')->nullable()->constrained('invoices')->cascadeOnDelete();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenancy_bills');
    }
};
