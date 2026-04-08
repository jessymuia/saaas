<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
            $table->uuid('saas_client_id')->nullable();

            $table->string('name');
            $table->string('email');
            $table->string('phone_number', 20)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();

            $table->decimal('version', 10, 2)->default(1.0);
            $table->timestampsTz();
            $table->softDeletes();
            $table->boolean('status')->default(true);
            $table->boolean('archive')->default(false);

            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();

            $table->index('saas_client_id');
            $table->unique(['email', 'saas_client_id'], 'users_email_unique');
            $table->unique(['phone_number', 'saas_client_id'], 'users_phone_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
