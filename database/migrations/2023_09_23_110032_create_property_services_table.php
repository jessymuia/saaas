<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_services', function (Blueprint $table) {
            $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: true, addAuditFk: true);

            $table->uuid('saas_client_id')->nullable()->index();
            $table->foreignUuid('property_id')->constrained('properties')->cascadeOnDelete();
            $table->foreignUuid('service_id')->constrained('services')->restrictOnDelete();
            $table->decimal('rate', 14, 2);
            $table->foreignUuid('billing_type_id')->constrained('ref_billing_types')->restrictOnDelete();

        });

        DB::statement('ALTER TABLE property_services ADD is_deleted BOOLEAN GENERATED ALWAYS AS (CASE WHEN deleted_at IS NULL THEN FALSE ELSE TRUE END) STORED');
        DB::statement('ALTER TABLE property_services ADD CONSTRAINT property_services_unique_index UNIQUE (property_id, service_id, is_deleted, saas_client_id)');
    }

    public function down(): void
    {
        Schema::dropIfExists('property_services');
    }
};
