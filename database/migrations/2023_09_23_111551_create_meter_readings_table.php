<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meter_readings', function (Blueprint $table) {
            
            $table->unsignedBigInteger('id')->autoIncrement();

            $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: false, addAuditFk: false);

            
            $table->uuid('saas_client_id');

            $table->unsignedBigInteger('unit_id');
            $table->unsignedBigInteger('utility_id');
            $table->dateTime('reading_date');
            $table->decimal('current_reading', 16, 5);
            $table->decimal('previous_reading', 16, 5);
            $table->decimal('consumption', 16, 5);
            $table->tinyInteger('has_bill')->default(0);

            
            $table->primary(['id', 'saas_client_id']);

           
            $table->foreign(['unit_id', 'saas_client_id'])
                  ->references(['id', 'saas_client_id'])
                  ->on('units')
                  ->onDelete('cascade');

            
            $table->foreign('utility_id')
                  ->references('id')
                  ->on('ref_utilities')
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
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meter_readings');
    }
};
