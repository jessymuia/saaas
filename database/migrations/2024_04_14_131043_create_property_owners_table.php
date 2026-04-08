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
            $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: true, addAuditFk: true);

            $table->uuid('saas_client_id')->nullable()->index();
            $table->foreignUuid('property_id')->constrained('properties')->cascadeOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('address');
            $table->string('tax_pin', 50)->nullable();
            $table->decimal('balance_carried_forward', 14, 2)->default(0);
            $table->boolean('has_invoice_for_balance_carried_forward')->default(false);

        });

        DB::statement('ALTER TABLE property_owners ADD is_deleted BOOLEAN GENERATED ALWAYS AS (CASE WHEN deleted_at IS NULL THEN FALSE ELSE TRUE END) STORED');
        DB::statement('ALTER TABLE property_owners ADD CONSTRAINT unique_ownership_of_properties_index UNIQUE (property_id, is_deleted, saas_client_id)');
    }

    public function down(): void
    {
        Schema::dropIfExists('property_owners');
    }
};
