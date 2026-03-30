<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usage_metrics', function (Blueprint $table) {
            $table->id();
            $table->uuid('saas_client_id')->index();
            $table->integer('properties_count')->default(0);
            $table->integer('units_count')->default(0);
            $table->integer('users_count')->default(0);
            $table->integer('tenants_count')->default(0);
            $table->integer('invoices_count')->default(0);
            $table->bigInteger('storage_used_kb')->default(0);
            $table->timestamp('last_calculated_at')->nullable();
            $table->timestamps();

            $table->foreign('saas_client_id')
                  ->references('id')
                  ->on('saas_clients')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usage_metrics');
    }
};