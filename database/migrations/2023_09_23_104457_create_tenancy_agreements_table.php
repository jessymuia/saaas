<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenancy_agreements', function (Blueprint $table) {
            $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: true, addAuditFk: true);

            $table->uuid('saas_client_id')->nullable()->index();
            $table->foreignUuid('unit_id')->constrained('units')->cascadeOnDelete();
            $table->foreignUuid('tenant_id')->constrained('tenants')->restrictOnDelete();
            $table->foreignUuid('agreement_type_id')->constrained('ref_tenancy_agreement_types')->restrictOnDelete();
            $table->foreignUuid('billing_type_id')->constrained('ref_billing_types')->restrictOnDelete();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('amount', 14, 2);
            $table->decimal('deposit_amount', 14, 2);
            $table->decimal('escalation_rate', 5, 2)->nullable();
            $table->integer('escalation_period_in_months')->nullable();
            $table->date('next_escalation_date')->nullable();
            $table->decimal('balance_carried_forward', 14, 2)->default(0);
            $table->boolean('has_invoice_for_balance_carried_forward')->default(false);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenancy_agreements');
    }
};
