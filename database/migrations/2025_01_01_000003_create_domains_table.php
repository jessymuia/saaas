<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('domains', function (Blueprint $table) {

            $table->id();

            
            $table->uuid('saas_client_id');

            $table->string('domain')->unique();

            $table->timestamps();

            
            $table->foreign('saas_client_id')
                  ->references('id')
                  ->on('saas_clients')
                  ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domains');
    }
};