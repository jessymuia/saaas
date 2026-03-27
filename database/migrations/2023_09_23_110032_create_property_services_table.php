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
            
            $table->unsignedBigInteger('id')->autoIncrement();

            $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: false, addAuditFk: false);

            
            $table->uuid('saas_client_id');

            $table->unsignedBigInteger('property_id');
            $table->unsignedBigInteger('service_id');
            $table->decimal('rate', 14, 2);
            $table->unsignedBigInteger('billing_type_id');

            
            $table->primary(['id', 'saas_client_id']);

           
            $table->foreign(['property_id', 'saas_client_id'])
                  ->references(['id', 'saas_client_id'])
                  ->on('properties')
                  ->onDelete('cascade');

            
            $table->foreign('service_id')
                  ->references('id')
                  ->on('services')
                  ->onDelete('restrict');

            $table->foreign('billing_type_id')
                  ->references('id')
                  ->on('ref_billing_types')
                  ->onDelete('restrict');

            
            $table->foreign(['created_by', 'saas_client_id'])
                  ->references(['id', 'saas_client_id'])
                  ->on('users')
                  ->cascadeOnDelete();

            $table->foreign(['updated_by', 'saas_client_id'])
                  ->references(['id', 'saas_client_id'])
                  ->on('users')
                  ->cascadeOnDelete();

            $table->foreign(['deleted_by', 'saas_client_id'])
                  ->references(['id', 'saas_client_id'])
                  ->on('users')
                  ->cascadeOnDelete();

            if (getenv('DB_CONNECTION') === 'mysql') {
                $table->boolean('is_deleted')
                    ->virtualAs('IF(deleted_at IS NULL, 0, 1)');
                $table->unique(
                    ['property_id', 'service_id', 'is_deleted', 'saas_client_id'],
                    'property_services_unique_index'
                );
            }
        });

        if (getenv('DB_CONNECTION') === 'pgsql') {
            DB::statement('ALTER TABLE property_services ADD is_deleted BOOLEAN GENERATED ALWAYS AS (CASE WHEN deleted_at IS NULL THEN FALSE ELSE TRUE END) STORED');
            DB::statement('ALTER TABLE property_services ADD CONSTRAINT property_services_unique_index UNIQUE (property_id, service_id, is_deleted, saas_client_id)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('property_services');
    }
};
