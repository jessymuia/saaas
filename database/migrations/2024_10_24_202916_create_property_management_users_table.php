<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_management_users', function (Blueprint $table) {
            $table->decimal('version', 10, 2)->default(1.0);
            $table->timestampsTz();
            $table->boolean('status')->default(true);
            $table->boolean('archive')->default(false);
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();

            $table->uuid('saas_client_id')->index();
            $table->foreignUuid('property_id')->constrained('properties')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();

            $table->primary(['property_id', 'user_id', 'saas_client_id']);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_management_users');
    }
};
