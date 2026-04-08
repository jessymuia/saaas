<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_attachments', function (Blueprint $table) {
            $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: true, addAuditFk: false);

            $table->uuid('saas_client_id')->nullable()->index();
            $table->foreignUuid('sent_email_id')->constrained('sent_emails')->cascadeOnDelete();
            $table->string('file_name');
            $table->integer('file_size');
            $table->string('mime_type');
            $table->string('full_file_path');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_attachments');
    }
};
