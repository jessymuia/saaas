<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_owners', function (Blueprint $table) {
            
            $table->unsignedBigInteger('id')->autoIncrement();

            $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: false, addAuditFk: false);

            
            $table->uuid('saas_client_id');

            $table->unsignedBigInteger('property_id');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('address');
            $table->string('tax_pin', 50)->nullable();
            $table->decimal('balance_carried_forward', 14, 2)->default(0);
            $table->boolean('has_invoice_for_balance_carried_forward')->default(false);

            
            $table->primary(['id', 'saas_client_id']);

            
            $table->foreign(['property_id', 'saas_client_id'])
                  ->references(['id', 'saas_client_id'])
                  ->on('properties')
                  ->onDelete('cascade');

            
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
                    ['property_id', 'is_deleted', 'saas_client_id'],
                    'unique_ownership_of_properties_index'
                );
            }
        });

        if (getenv('DB_CONNECTION') === 'pgsql') {
            DB::statement('ALTER TABLE property_owners ADD is_deleted BOOLEAN GENERATED ALWAYS AS (CASE WHEN deleted_at IS NULL THEN FALSE ELSE TRUE END) STORED');
            DB::statement('ALTER TABLE property_owners ADD CONSTRAINT unique_ownership_of_properties_index UNIQUE (property_id, is_deleted, saas_client_id)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('property_owners');
    }
};
