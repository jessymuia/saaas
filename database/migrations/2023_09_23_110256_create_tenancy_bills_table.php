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
        Schema::create('tenancy_bills', function (Blueprint $table) {
            $table = \App\Utils\AppUtils::defaultTableColumns($table);

            $table->unsignedBigInteger('tenancy_agreement_id');
            $table->string('name',500);
            $table->date('bill_date');
            $table->date('due_date');
            $table->decimal('amount', 14, 2);
            $table->unsignedBigInteger('billing_type_id');
            $table->unsignedBigInteger('service_id')->nullable();
            $table->unsignedBigInteger('utility_id')->nullable();

            // foreign keys
            $table->foreign('tenancy_agreement_id')->references('id')->on('tenancy_agreements');
            $table->foreign('billing_type_id')->references('id')->on('ref_billing_types');
            $table->foreign('service_id')->references('id')->on('services');
            $table->foreign('utility_id')->references('id')->on('ref_utilities');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenancy_bills');
    }
};
