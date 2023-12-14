<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('email_attachments', function (Blueprint $table) {
            $table = \App\Utils\AppUtils::defaultTableColumns($table);

            $table->unsignedBigInteger('sent_email_id');
            $table->string('file_name');
            $table->integer('file_size');
            $table->string('mime_type');
            $table->string('full_file_path');

            // foreign keys
            $table->foreign('sent_email_id')->references('id')->on('sent_emails');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_attachments');
    }
};
