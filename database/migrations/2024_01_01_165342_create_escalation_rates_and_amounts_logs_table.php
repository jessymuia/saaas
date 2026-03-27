<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('escalation_rates_and_amounts_logs', function (Blueprint $table) {
           
            $table->unsignedBigInteger('id')->autoIncrement();

            $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: false, addAuditFk: false);

            
            $table->uuid('saas_client_id');

            $table->unsignedBigInteger('tenancy_agreement_id');
            $table->unsignedBigInteger('property_id');
            $table->decimal('escalation_rate', 5, 2);
            $table->decimal('previous_amount', 14, 2);
            $table->decimal('new_amount', 14, 2);
            $table->date('escalation_date');

            
            $table->primary(['id', 'saas_client_id']);

            
            $table->foreign(['tenancy_agreement_id', 'saas_client_id'])
                  ->references(['id', 'saas_client_id'])
                  ->on('tenancy_agreements')
                  ->onDelete('cascade');

            $table->foreign(['property_id', 'saas_client_id'])
                  ->references(['id', 'saas_client_id'])
                  ->on('properties')
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
        Schema::dropIfExists('escalation_rates_and_amounts_logs');
    }
};
