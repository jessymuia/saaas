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
        Schema::create('sent_emails', function (Blueprint $table) {
            $table = \App\Utils\AppUtils::defaultTableColumns($table);

            $table->string('recipient_email');
            $table->string('subject');
            $table->unsignedBigInteger('reference_id')->nullable(); # id of the referenced item
            $table->text('body');
            $table->enum('delivery_status', ['SENT', 'FAILED','PENDING'])
                ->default('PENDING');
            $table->text('failure_reason')->nullable();
            $table->timestamp('read_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sent_emails');
    }
};
