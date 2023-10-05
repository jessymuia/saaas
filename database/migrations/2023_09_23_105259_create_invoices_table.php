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
        Schema::create('invoices', function (Blueprint $table) {
            $table = \App\Utils\AppUtils::defaultTableColumns($table);

            $table->unsignedBigInteger('tenancy_agreement_id');
            $table->string('comments',1000)->nullable();
            $table->string('invoice_status',20)->nullable();
            $table->date('issue_date');

            // foreign keys
            $table->foreign('tenancy_agreement_id')->references('id')->on('tenancy_agreements');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
