<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_payment_details', function (Blueprint $table) {
            $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: true, addAuditFk: false);

            $table->uuid('saas_client_id')->nullable()->index();
            $table->foreignUuid('property_id')->constrained('properties')->cascadeOnDelete();
            $table->string('account_name');
            $table->string('account_number');
            $table->string('bank_name');
            $table->string('mpesa_paybill_number');

            $table->unique(['property_id', 'saas_client_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_payment_details');
    }
};
