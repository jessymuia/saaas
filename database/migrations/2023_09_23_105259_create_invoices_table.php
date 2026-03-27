<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            
            $table->unsignedBigInteger('id')->autoIncrement();

            $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: false, addAuditFk: false);

            
            $table->uuid('saas_client_id');

            $table->unsignedBigInteger('tenancy_agreement_id');
            $table->string('comments', 1000)->nullable();
            $table->string('invoice_status', 20)->nullable();
            $table->date('issue_date')->nullable();
            $table->date('invoice_for_month');
            $table->date('invoice_due_date')->nullable();
            $table->tinyInteger('is_confirmed')->default(0);
            $table->tinyInteger('is_generated')->default(0);
            $table->string('document_url')->nullable();

            
            $table->primary(['id', 'saas_client_id']);

           
            $table->foreign(['tenancy_agreement_id', 'saas_client_id'])
                  ->references(['id', 'saas_client_id'])
                  ->on('tenancy_agreements')
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
        Schema::dropIfExists('invoices');
    }
};
