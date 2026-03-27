<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manual_invoice_items', function (Blueprint $table) {
            
            $table->unsignedBigInteger('id')->autoIncrement();

            $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: false, addAuditFk: false);

            
            $table->uuid('saas_client_id');

            $table->unsignedBigInteger('manual_invoice_id');
            $table->string('name', 500);
            $table->date('bill_date');
            $table->date('due_date');
            $table->integer('quantity')->default(1);
            $table->decimal('amount', 14, 2);
            $table->decimal('vat', 14, 2)->default(0.0);
            $table->decimal('total_amount', 14, 2);
            $table->unsignedBigInteger('billing_type_id');
            $table->enum('category', ['ordinary', 'balance_carried_forward'])->default('ordinary');

            
            $table->primary(['id', 'saas_client_id']);

            
            $table->foreign(['manual_invoice_id', 'saas_client_id'])
                  ->references(['id', 'saas_client_id'])
                  ->on('manual_invoices')
                  ->onDelete('cascade');

           
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
        Schema::dropIfExists('manual_invoice_items');
    }
};
