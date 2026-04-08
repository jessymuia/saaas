<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: true, addAuditFk: false);

            $table->uuid('saas_client_id')->nullable()->index();
            $table->string('name');
            $table->foreignUuid('property_id')->constrained('properties')->cascadeOnDelete();
            $table->foreignUuid('unit_type_id')->constrained('ref_unit_types')->restrictOnDelete();
            $table->decimal('area_in_square_feet', 14, 2)->default(0.0);

        });

        DB::statement('ALTER TABLE units ADD is_deleted BOOLEAN GENERATED ALWAYS AS (CASE WHEN deleted_at IS NULL THEN FALSE ELSE TRUE END) STORED');
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
