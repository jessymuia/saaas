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
            $table->unsignedBigInteger('id')->autoIncrement(); 
            $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: false, addAuditFk: false);

            $table->uuid('saas_client_id');
            $table->string('name');
            $table->unsignedBigInteger('property_id');
            $table->unsignedBigInteger('unit_type_id');
            $table->decimal('area_in_square_feet', 14, 2)->default(0.0);

            
            $table->index('created_by');
            $table->index('updated_by');
            $table->index('deleted_by');

            $table->primary(['id', 'saas_client_id']);

            $table->foreign(['property_id', 'saas_client_id'])
                  ->references(['id', 'saas_client_id'])
                  ->on('properties')
                  ->onDelete('cascade');

            $table->foreign('unit_type_id')
                  ->references('id')
                  ->on('ref_unit_types')
                  ->onDelete('restrict');
        });

        if (config('database.default') === 'pgsql') {
            DB::statement('ALTER TABLE units ADD is_deleted BOOLEAN GENERATED ALWAYS AS (CASE WHEN deleted_at IS NULL THEN FALSE ELSE TRUE END) STORED');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
