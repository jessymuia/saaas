<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: true, addAuditFk: true);

            $table->uuid('saas_client_id')->nullable()->index();
            $table->foreignUuid('tenancy_agreement_id')->constrained('tenancy_agreements')->cascadeOnDelete();
            $table->string('comments', 1000)->nullable();
            $table->string('invoice_status', 20)->nullable();
            $table->date('issue_date')->nullable();
            $table->date('invoice_for_month');
            $table->date('invoice_due_date')->nullable();
            $table->tinyInteger('is_confirmed')->default(0);
            $table->tinyInteger('is_generated')->default(0);
            $table->string('document_url')->nullable();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
