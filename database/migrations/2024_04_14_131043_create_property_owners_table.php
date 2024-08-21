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
        Schema::create('property_owners', function (Blueprint $table) {
            $table = \App\Utils\AppUtils::defaultTableColumns($table);

            $table->unsignedBigInteger('property_id')->unique();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('address');
            $table->decimal('balance_carried_forward',14,2)->default(0);
            $table->boolean('has_invoice_for_balance_carried_forward')->default(false);

            if (getenv('DB_CONNECTION') === 'mysql'){
                $table->boolean('is_deleted')
                    ->virtualAs('IF(deleted_at IS NULL, 0, 1)');
                $table->unique(['property_id', 'is_deleted'], 'unique_ownership_of_properties_index');
            }

            // foreign keys
            $table->foreign('property_id')->references('id')->on('properties');
        });

        if (getenv('DB_CONNECTION') === 'pgsql'){
            DB::statement('ALTER TABLE property_owners ADD is_deleted BOOLEAN GENERATED ALWAYS AS (CASE WHEN deleted_at IS NULL THEN FALSE ELSE TRUE END) STORED');
            DB::statement('ALTER TABLE property_owners ADD CONSTRAINT unique_ownership_of_properties_index UNIQUE (property_id, is_deleted)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_owners');
    }
};
