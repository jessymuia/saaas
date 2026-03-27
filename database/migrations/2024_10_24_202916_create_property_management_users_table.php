<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_management_users', function (Blueprint $table) {
            
            $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: false, addAuditFk: false);

            $table->uuid('saas_client_id');
            $table->unsignedBigInteger('property_id');
            $table->unsignedBigInteger('user_id');
            
            

            $table->primary(['property_id', 'user_id', 'saas_client_id']);

            
            $table->foreign(['property_id', 'saas_client_id'])
                  ->references(['id', 'saas_client_id'])
                  ->on('properties')
                  ->onDelete('cascade');

            $table->foreign(['user_id', 'saas_client_id'])
                  ->references(['id', 'saas_client_id'])
                  ->on('users')
                  ->onDelete('cascade');

           
            $table->foreign(['created_by', 'saas_client_id'])
                  ->references(['id', 'saas_client_id'])
                  ->on('users')
                  ->cascadeOnDelete();
            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_management_users');
    }
};
