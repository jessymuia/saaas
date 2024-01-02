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
        Schema::create('tenancy_agreements', function (Blueprint $table) {
            $table = \App\Utils\AppUtils::defaultTableColumns($table);

            $table->unsignedBigInteger('unit_id');
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('agreement_type_id');
            $table->unsignedBigInteger('billing_type_id');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('amount', 14, 2);
            $table->decimal('escalation_rate',5,2)->nullable();
            $table->integer('escalation_period_in_months')->nullable();
            $table->date('next_escalation_date')->nullable();

            // foreign  keys
            $table->foreign('unit_id')->references('id')->on('units');
            $table->foreign('tenant_id')->references('id')->on('tenants');
            $table->foreign('agreement_type_id')->references('id')->on('ref_tenancy_agreement_types');
            $table->foreign('billing_type_id')->references('id')->on('ref_billing_types');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenancy_agreements');
    }
};
