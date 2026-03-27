<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            
            
            $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: false, addAuditFk: false);
            
            $table->uuid('saas_client_id');
            $table->string('name');
            $table->string('address');
            $table->integer('number_of_units')->nullable();
            $table->unsignedBigInteger('property_type_id')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_vatable')->default(false);

            
            $table->index('created_by');
            $table->index('updated_by');
            $table->index('deleted_by');

            $table->primary(['id', 'saas_client_id']);

            $table->foreign('property_type_id')
                  ->references('id')
                  ->on('ref_property_types')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
