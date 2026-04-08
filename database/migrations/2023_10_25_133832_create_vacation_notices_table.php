<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vacation_notices', function (Blueprint $table) {
            $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: true, addAuditFk: true);

            $table->uuid('saas_client_id')->nullable()->index();
            $table->foreignUuid('tenancy_agreement_id')->constrained('tenancy_agreements')->cascadeOnDelete();
            $table->foreignUuid('property_id')->constrained('properties')->restrictOnDelete();
            $table->dateTime('notice_start_date');
            $table->dateTime('notice_end_date');
            $table->text('extra_information')->nullable();
            $table->string('document_url')->nullable();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vacation_notices');
    }
};
