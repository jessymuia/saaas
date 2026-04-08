<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: true, addAuditFk: false);

            $table->uuid('saas_client_id')->nullable()->index();
            $table->string('name');
            $table->string('address');
            $table->integer('number_of_units')->nullable();
            $table->foreignUuid('property_type_id')->nullable()->constrained('ref_property_types')->nullOnDelete();
            $table->text('description')->nullable();
            $table->boolean('is_vatable')->default(false);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
