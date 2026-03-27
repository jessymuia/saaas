<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vacation_notices', function (Blueprint $table) {
            
            $table->unsignedBigInteger('id')->autoIncrement();

            $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: false, addAuditFk: false);

           
            $table->uuid('saas_client_id');

            $table->unsignedBigInteger('tenancy_agreement_id');
            $table->unsignedBigInteger('property_id');
            $table->dateTime('notice_start_date');
            $table->dateTime('notice_end_date');
            $table->text('extra_information')->nullable();
            $table->string('document_url')->nullable();

            
            $table->primary(['id', 'saas_client_id']);

            
            $table->foreign(['tenancy_agreement_id', 'saas_client_id'])
                  ->references(['id', 'saas_client_id'])
                  ->on('tenancy_agreements')
                  ->onDelete('cascade');

            $table->foreign(['property_id', 'saas_client_id'])
                  ->references(['id', 'saas_client_id'])
                  ->on('properties')
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
        Schema::dropIfExists('vacation_notices');
    }
};
