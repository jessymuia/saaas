<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('users', function (Blueprint $table) {
        $table->id();                    
        $table->uuid('saas_client_id');   

        // Basic user fields
        $table->string('name');
        $table->string('email');
        $table->string('phone_number', 20);
        $table->timestamp('email_verified_at')->nullable();
        $table->string('password');
        $table->rememberToken();

        $table->timestamps();
        $table->softDeletes();

        // Audit columns (we'll link them properly later)
        $table->unsignedBigInteger('created_by')->nullable();
        $table->unsignedBigInteger('updated_by')->nullable();
        $table->unsignedBigInteger('deleted_by')->nullable();

        // Indexes
        $table->index('saas_client_id');
        $table->index(['email', 'saas_client_id']);
        $table->index(['phone_number', 'saas_client_id']);

        // Unique constraints with tenant scope
        $table->unique(['email', 'saas_client_id'], 'users_email_unique');
        $table->unique(['phone_number', 'saas_client_id'], 'users_phone_unique');

        // Composite Primary Key (required for Citus)
        $table->primary(['id', 'saas_client_id']);
    });
}

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
