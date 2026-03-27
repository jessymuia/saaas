<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenancy_agreements', function (Blueprint $table) {
            
            $table->unsignedBigInteger('id')->autoIncrement();

            $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: false, addAuditFk: false);

            
            $table->uuid('saas_client_id');

            $table->unsignedBigInteger('unit_id');
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('agreement_type_id');
            $table->unsignedBigInteger('billing_type_id');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('amount', 14, 2);
            $table->decimal('deposit_amount', 14, 2);
            $table->decimal('escalation_rate', 5, 2)->nullable();
            $table->integer('escalation_period_in_months')->nullable();
            $table->date('next_escalation_date')->nullable();
            $table->decimal('balance_carried_forward', 14, 2)->default(0);
            $table->boolean('has_invoice_for_balance_carried_forward')->default(false);

            
            $table->primary(['id', 'saas_client_id']);

            
            $table->foreign(['unit_id', 'saas_client_id'])
                  ->references(['id', 'saas_client_id'])
                  ->on('units')
                  ->onDelete('cascade');

            $table->foreign(['tenant_id', 'saas_client_id'])
                  ->references(['id', 'saas_client_id'])
                  ->on('tenants')
                  ->onDelete('restrict');

           
            $table->foreign('agreement_type_id')
                  ->references('id')
                  ->on('ref_tenancy_agreement_types')
                  ->onDelete('restrict');

            $table->foreign('billing_type_id')
                  ->references('id')
                  ->on('ref_billing_types')
                  ->onDelete('restrict');

            
            $table->foreign(['created_by', 'saas_client_id'])
                  ->references(['id', 'saas_client_id'])
                  ->on('users')
                  ->cascadeOnDelete();

            $table->foreign(['updated_by', 'saas_client_id'])
                  ->references(['id', 'saas_client_id'])
                  ->on('users')
                  ->cascadeOnDelete();

            $table->foreign(['deleted_by', 'saas_client_id'])
                  ->references(['id', 'saas_client_id'])
                  ->on('users')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenancy_agreements');
    }
};
