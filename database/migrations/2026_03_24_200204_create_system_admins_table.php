<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_admins', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));

            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();

            $table->decimal('version', 10, 2)->default(1.0);
            $table->timestampsTz();
            $table->softDeletes();
            $table->boolean('status')->default(true);
            $table->boolean('archive')->default(false);

            // Audit columns — plain UUID (no FK) because system_admins are not
            // regular tenant users; the 'users' table holds tenants only.
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_admins');
    }
};
