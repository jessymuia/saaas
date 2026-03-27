<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenancy_bills', function (Blueprint $table) {
            
            $table->unsignedBigInteger('id')->autoIncrement();

            $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: false, addAuditFk: false);

            
            $table->uuid('saas_client_id');

            $table->unsignedBigInteger('tenancy_agreement_id');
            $table->string('name', 500);
            $table->date('bill_date');
            $table->date('due_date');
            $table->decimal('amount', 14, 2);
            $table->decimal('vat', 14, 2)->default(0.0);
            $table->decimal('total_amount', 14, 2);
            $table->unsignedBigInteger('billing_type_id');
            $table->unsignedBigInteger('service_id')->nullable();
            $table->unsignedBigInteger('utility_id')->nullable();
            $table->boolean('is_deposit')->default(false);
            $table->unsignedBigInteger('invoice_id')->nullable();

            
            $table->primary(['id', 'saas_client_id']);

            
            $table->foreign(['tenancy_agreement_id', 'saas_client_id'])
                  ->references(['id', 'saas_client_id'])
                  ->on('tenancy_agreements')
                  ->onDelete('cascade');

            $table->foreign(['invoice_id', 'saas_client_id'])
                  ->references(['id', 'saas_client_id'])
                  ->on('invoices')
                  ->cascadeOnDelete();

           
            $table->foreign('billing_type_id')
                  ->references('id')
                  ->on('ref_billing_types')
                  ->onDelete('restrict');

            $table->foreign('service_id')
                  ->references('id')
                  ->on('services')
                  ->cascadeOnDelete();

            $table->foreign('utility_id')
                  ->references('id')
                  ->on('ref_utilities')
                  ->cascadeOnDelete();

           
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
        Schema::dropIfExists('tenancy_bills');
    }
};
