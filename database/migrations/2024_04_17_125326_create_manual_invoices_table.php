<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manual_invoices', function (Blueprint $table) {
            $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: true, addAuditFk: true);

            $table->uuid('saas_client_id')->nullable()->index();
            $table->foreignUuid('property_owner_id')->nullable()->constrained('property_owners')->cascadeOnDelete();
            $table->foreignUuid('client_id')->nullable()->constrained('clients')->cascadeOnDelete();
            $table->foreignUuid('tenant_id')->nullable()->constrained('tenants')->cascadeOnDelete();
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
        Schema::dropIfExists('manual_invoices');
    }
};
