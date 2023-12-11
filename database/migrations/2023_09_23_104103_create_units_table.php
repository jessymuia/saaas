<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table = \App\Utils\AppUtils::defaultTableColumns($table);

            $table->string('name');
            $table->unsignedBigInteger('property_id');
            $table->unsignedBigInteger('unit_type_id');
            if (getenv('DB_CONNECTION') === 'mysql'){
                $table->boolean('is_deleted')
                    ->virtualAs('IF(deleted_at IS NULL, 0, 1)');
            }

            // foreign keys
            $table->foreign('property_id')->references('id')->on('properties');
            $table->foreign('unit_type_id')->references('id')->on('ref_unit_types');

            if (getenv('DB_CONNECTION') === 'mysql'){
                // constraint to ensure uniqueness of name and property_id
                $table->unique(['name', 'property_id', 'is_deleted'], 'units_unique_index');
            }
        });

        if (getenv('DB_CONNECTION') === 'pgsql'){
            DB::statement('ALTER TABLE units ADD is_deleted BOOLEAN GENERATED ALWAYS AS (CASE WHEN deleted_at IS NULL THEN FALSE ELSE TRUE END) STORED');
            DB::statement('ALTER TABLE units ADD CONSTRAINT units_unique_index UNIQUE (name, property_id, is_deleted)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
