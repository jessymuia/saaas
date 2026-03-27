<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saas_client_users', function (Blueprint $table) {

            $table->id();

            $table->uuid('saas_client_id');              
            $table->unsignedBigInteger('user_id');        
            $table->uuid('user_saas_client_id'); 

            $table->string('role')->nullable();

            $table->timestamps();

            
            $table->foreign('saas_client_id')
                ->references('id')
                ->on('saas_clients')
                ->onDelete('cascade');

            
            $table->foreign(['user_id', 'user_saas_client_id'])
                ->references(['id', 'saas_client_id'])
                ->on('users')
                ->onDelete('cascade');

            $table->index(['saas_client_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saas_client_users');
    }
};
