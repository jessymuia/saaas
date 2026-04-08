<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenancy_agreement_files', function (Blueprint $table) {
            $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: true, addAuditFk: false);

            $table->uuid('saas_client_id')->nullable()->index();
            $table->foreignUuid('tenancy_agreement_id')->constrained('tenancy_agreements')->cascadeOnDelete();
            $table->string('name');
            $table->string('path');
            $table->string('mime_type', 20);
            $table->string('extension', 10);
            $table->integer('size');
            $table->string('description')->nullable();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenancy_agreement_files');
    }
};
