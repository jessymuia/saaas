<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sent_emails', function (Blueprint $table) {
            
            $table->unsignedBigInteger('id')->autoIncrement();

            $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: false, addAuditFk: false);

           
            $table->uuid('saas_client_id');

            $table->string('recipient_email');
            $table->string('subject');
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('body');
            $table->enum('delivery_status', ['SENT', 'FAILED', 'PENDING'])->default('PENDING');
            $table->text('failure_reason')->nullable();
            $table->timestamp('read_at')->nullable();

            
            $table->primary(['id', 'saas_client_id']);

            
            $table->foreign(['created_by', 'saas_client_id'])
                  ->references(['id', 'saas_client_id'])
                  ->on('users')
                  ->cascadeOnDelete();

            $table->foreign(['updated_by', 'saas_client_id'])
                  ->references(['id', 'saas_client_id'])
                  ->on('users')
                  ->cascadeOnDelete();

            $table->foreign(['deleted_by', 'saas_client_id'])
                  ->references(['id', 'saas_client_id'])
                  ->on('users')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sent_emails');
    }
};
