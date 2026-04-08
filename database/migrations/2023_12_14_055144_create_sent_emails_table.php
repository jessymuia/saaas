<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sent_emails', function (Blueprint $table) {
            $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: true, addAuditFk: true);

            $table->uuid('saas_client_id')->nullable()->index();
            $table->string('recipient_email');
            $table->string('subject');
            $table->uuid('reference_id')->nullable();
            $table->text('body');
            $table->enum('delivery_status', ['SENT', 'FAILED', 'PENDING'])->default('PENDING');
            $table->text('failure_reason')->nullable();
            $table->timestamp('read_at')->nullable();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sent_emails');
    }
};
