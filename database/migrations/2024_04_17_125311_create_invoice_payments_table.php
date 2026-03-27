<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_payments', function (Blueprint $table) {
            
            $table->unsignedBigInteger('id')->autoIncrement();

            $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: false, addAuditFk: false);

           
            $table->uuid('saas_client_id');

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

           
            $table->primary(['id', 'saas_client_id']);

            
            $table->foreign(['invoice_id', 'saas_client_id'])
                  ->references(['id', 'saas_client_id'])
                  ->on('invoices')
                  ->onDelete('cascade');

            $table->foreign(['tenant_id', 'saas_client_id'])
                  ->references(['id', 'saas_client_id'])
                  ->on('tenants')
                  ->cascadeOnDelete();

            
            $table->foreign(['client_id', 'saas_client_id'])
                  ->references(['id', 'saas_client_id'])
                  ->on('clients')
                  ->cascadeOnDelete();

            $table->foreign(['property_owner_id', 'saas_client_id'])
                  ->references(['id', 'saas_client_id'])
                  ->on('property_owners')
                  ->cascadeOnDelete();

           
            $table->foreign('payment_type_id')
                  ->references('id')
                  ->on('ref_payment_types')
                  ->onDelete('restrict');

            
            $table->foreign(['received_by', 'saas_client_id'])
                  ->references(['id', 'saas_client_id'])
                  ->on('users')
                  ->onDelete('restrict');

            $table->foreign(['document_generated_by', 'saas_client_id'])
                  ->references(['id', 'saas_client_id'])
                  ->on('users')
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
        Schema::dropIfExists('invoice_payments');
    }
};
