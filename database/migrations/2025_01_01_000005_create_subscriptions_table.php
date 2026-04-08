<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: true, addAuditFk: true, addStatus: false);

            $table->foreignUuid('saas_client_id')->constrained('saas_clients')->cascadeOnDelete();
            $table->foreignUuid('plan_id')->constrained('plans')->restrictOnDelete();
            $table->string('status', 20)->default('trialing')
                ->comment('trialing | active | past_due | cancelled | expired');
            $table->string('billing_cycle', 10)->default('monthly')
                ->comment('monthly | yearly');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
