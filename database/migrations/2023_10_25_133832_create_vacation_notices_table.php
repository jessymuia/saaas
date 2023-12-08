<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vacation_notices', function (Blueprint $table) {
            $table = \App\Utils\AppUtils::defaultTableColumns($table);

            $table->unsignedBigInteger('tenancy_agreement_id');
            $table->unsignedBigInteger('property_id');
            $table->dateTime('notice_start_date');
            $table->dateTime('notice_end_date');
            $table->text('extra_information')->nullable();
            $table->string('document_url')->nullable();

            // foreign keys
            $table->foreign('tenancy_agreement_id')->references('id')->on('tenancy_agreements');
            $table->foreign('property_id')->references('id')->on('properties');
        });
        DB::statement('SET SESSION sql_require_primary_key = ON;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vacation_notices');
    }
};
