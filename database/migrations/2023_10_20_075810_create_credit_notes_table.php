<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('credit_notes', function (Blueprint $table) {
            
            $table->unsignedBigInteger('id')->autoIncrement();

            $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: false, addAuditFk: false);

           
            $table->uuid('saas_client_id');

            $table->string('name', 500);
            $table->unsignedBigInteger('invoice_id');
            $table->dateTime('issue_date')->nullable();
            $table->text('reason_for_issuance')->nullable();
            $table->decimal('amount_credited', 14, 2);
            $table->text('notes')->nullable();
            $table->string('document_url')->nullable();
            $table->tinyInteger('is_confirmed')->default(0);
            $table->tinyInteger('is_document_generated')->default(0);

           
            $table->primary(['id', 'saas_client_id']);

            
            $table->foreign(['invoice_id', 'saas_client_id'])
                  ->references(['id', 'saas_client_id'])
                  ->on('invoices')
                  ->onDelete('cascade');

            
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
        Schema::dropIfExists('credit_notes');
    }
};
