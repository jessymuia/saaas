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
        Schema::create('credit_notes', function (Blueprint $table) {
            $table = \App\Utils\AppUtils::defaultTableColumns($table);

            $table->string('name',500);
            $table->unsignedBigInteger('invoice_id');
            $table->dateTime('issue_date')->nullable();
            $table->text('reason_for_issuance')->nullable();
            $table->decimal('amount_credited', 14, 2);
            $table->text('notes')->nullable();
            $table->string('document_url')->nullable();
            $table->tinyInteger('is_confirmed')->default(0);
            $table->tinyInteger('is_document_generated')->default(0);

            // foreign keys
            $table->foreign('invoice_id')->references('id')->on('invoices');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_notes');
    }
};
