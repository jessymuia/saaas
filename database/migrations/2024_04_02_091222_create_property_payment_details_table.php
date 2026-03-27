<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_payment_details', function (Blueprint $table) {
            
            $table->unsignedBigInteger('id')->autoIncrement();

            $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: false, addAuditFk: false);

            
            $table->uuid('saas_client_id');

            $table->unsignedBigInteger('property_id');
            $table->string('account_name');
            $table->string('account_number');
            $table->string('bank_name');
            $table->string('mpesa_paybill_number');

            
            $table->primary(['id', 'saas_client_id']);

            
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

            
            $table->unique(['property_id', 'saas_client_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_payment_details');
    }
};
