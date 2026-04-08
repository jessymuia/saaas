<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_payments', function (Blueprint $table) {
            $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: true, addAuditFk: true, addStatus: false);

            $table->foreignUuid('subscription_id')->constrained('subscriptions')->cascadeOnDelete();
            $table->foreignUuid('saas_client_id')->constrained('saas_clients')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 5)->default('KES');
            $table->string('payment_method', 20)->default('mpesa')
                ->comment('mpesa | bank_transfer | manual');
            $table->string('mpesa_ref', 50)->nullable()->comment('M-Pesa transaction code e.g. QHX7YZ123');
            $table->string('status', 20)->default('pending')
                ->comment('pending | successful | failed');
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->string('failure_reason', 500)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_payments');
    }
};
