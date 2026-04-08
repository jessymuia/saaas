<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_management_users', function (Blueprint $table) {
            $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: true, addAuditFk: true);

            $table->uuid('saas_client_id')->index();
            $table->foreignUuid('property_id')->constrained('properties')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();

            // The uuid 'id' from defaultTableColumns is the PK;
            // enforce uniqueness on the logical composite key instead.
            $table->unique(['property_id', 'user_id', 'saas_client_id'], 'pmu_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_management_users');
    }
};
