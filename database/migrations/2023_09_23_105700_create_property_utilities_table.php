<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('property_utilities', function (Blueprint $table) {
            $table = \App\Utils\AppUtils::defaultTableColumns($table);

            $table->unsignedBigInteger('property_id');
            $table->unsignedBigInteger('utility_id');
            $table->decimal('rate_per_unit', 14, 2);
            $table->unsignedBigInteger('billing_type_id');
            if (getenv('DB_CONNECTION') === 'mysql'){
                $table->boolean('is_deleted')
                    ->virtualAs('IF(deleted_at IS NULL, 0, 1)');
            }

            // foreign keys
            $table->foreign('property_id')->references('id')->on('properties');
            $table->foreign('utility_id')->references('id')->on('ref_utilities');
            $table->foreign('billing_type_id')->references('id')->on('ref_billing_types');

            if (getenv('DB_CONNECTION') === 'mysql'){
                // constraint to ensure uniqueness of property_id and utility_id
                // unique keys
                $table->unique(['property_id', 'utility_id', 'is_deleted'], 'property_utilities_unique_index');
            }
        });

        if (getenv('DB_CONNECTION') === 'pgsql'){
            DB::statement('ALTER TABLE property_utilities ADD is_deleted BOOLEAN GENERATED ALWAYS AS (CASE WHEN deleted_at IS NULL THEN FALSE ELSE TRUE END) STORED');
            DB::statement('ALTER TABLE property_utilities ADD CONSTRAINT property_utilities_unique_index UNIQUE (property_id, utility_id, is_deleted)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_utilities');
    }
};
