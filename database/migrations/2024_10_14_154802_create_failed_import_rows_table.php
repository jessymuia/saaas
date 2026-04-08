<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('failed_import_rows', function (Blueprint $table) {
            $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: true, addAuditFk: false);

            $table->uuid('saas_client_id')->nullable()->index();
            $table->json('data');
            $table->text('validation_error')->nullable();
            $table->foreignUuid('import_id')->constrained('imports')->cascadeOnDelete();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('failed_import_rows');
    }
};
